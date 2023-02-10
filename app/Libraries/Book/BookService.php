<?php


namespace LaravelSupports\Libraries\Book;


use App\Services\Images\AWSUploadService;
use FlyBookModels\Books\AuthorModel;
use FlyBookModels\Books\BookAuthorModel;
use FlyBookModels\Books\BookModel;
use FlyBookModels\Books\BookPackingModel;
use FlyBookModels\Configs\CategoriesModel;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image;
use LaravelSupports\Libraries\BookLibrary\Api\NaverSearchBookAPI;
use LaravelSupports\Libraries\BookLibrary\Api\Response\Items\NaverSearchBookResponseItem;

class BookService
{
    /**
     * 도서 DB가 없을 경우 생성하여 제공
     *
     * @param string $isbn
     * @param string $title
     * @param string $imageUrl
     * @param NaverSearchBookResponseItem|null $model
     * @return BookModel
     * @throws \Exception
     * @author  seul
     * @added   2021/04/09
     * @updated 2021/04/09
     * @updated 2021/04/26
     * @author  WilsonParker
     */
    public function findOrCreateBook(string $isbn, string $title, string $imageUrl, NaverSearchBookResponseItem $naverBookInfo = null)
    {
        $bookTitle = html_entity_decode(strip_tags($title));

        $isbn = strip_tags($isbn);

        /**
         * naver api 에서는
         * isbn : isbn10 isbn13
         * 형식으로 제공하기 때문에 isbn13 을 추출하기 위한 작업
         *
         * @author  WilsonParker
         * @added   2020/09/08
         * @updated 2020/09/08
         */
        if (strpos($isbn, " ") !== false) {
            // $isbn = substr(strip_tags($isbn), strpos($isbn, " ") + 1);
            $isbn = explode(' ', strip_tags($isbn))[1];
        }
        if (strpos($imageUrl, '?') !== false) {
            // $imageUrl = substr($imageUrl, 0, strpos($imageUrl, '?'));
            $imageUrl = explode('?', $imageUrl)[0];
        }

        /**
         * isbn 과 title 로 검색하여 같은 책이 자꾸 등록되는 문제가 있어서
         * isbn 으로만 검색하도록 설정
         * created_at 이 최신인 책을 가져옵니다
         *
         * @author  WilsonParker
         * @added   2020/03/24
         * @updated 2020/03/24
         * @updated 2020/09/08
         * isbn 으로 고유키를 설정할 수 없는 상황들이 존재하여 (ex, 잡지 등)
         * 책 제목과 isbn 을 이용하여 검색
         */
        // $book = Book::where('isbn', $isbn)->orderByDesc("created_at")->first();
        $book = BookModel::where('isbn', $isbn)->where('title', $bookTitle)->orderByDesc("created_at")->first();

        if (isset($book)) {
            return $book;
        }

        $category_id = 0;
        $book_page = 0;
        $full_description2 = '';

        if (!isset($naverBookInfo)) {
            $api = new NaverSearchBookAPI();
            $naverBookInfo = $api->searchForISBN($isbn);

            if (isset($naverBookInfo->items[0])) {
                $naverBookInfo = $naverBookInfo->items[0];
            }

            if (!isset($naverBookInfo->description)) {
                throw new \Exception('책정보데이터가 없습니다.');
            }
        }

        $full_description2 = $naverBookInfo->description;

        /**
         *
         * @author  WilsonParker
         * @added   2020/03/24
         * @updated 2020/03/24
         */
        $book = new BookModel();
        $book->title = $bookTitle;
        $book->book_img = $imageUrl;
        $book->isbn = $isbn;
        $book->author = $naverBookInfo->author;
        $book->publisher = $naverBookInfo->publisher;
        $book->pubdate = $naverBookInfo->pubdate;
        $book->description = $naverBookInfo->description;
        $book->category_id = $category_id;
        $book->description_html = $full_description2;
        $book->page = $book_page;
        $book->save();

        $this->updateAladinInfo($book);

        $book->share_img = $this->makeBookShareImg($book);
        $book->save();
        return $book;
    }

    public function updateAladinInfo($book)
    {
        $url = 'http://www.aladin.co.kr/ttb/api/ItemLookUp.aspx';
        $client = new Client();
        $response = $client->request('GET', $url, [
            'query' => [
                'ttbkey' => 'ttbceo1219002',
                'itemIdType' => 'ISBN13',
                'ItemId' => $book->isbn,
                'output' => 'JS',
                'Version' => '20131101',
                'Cover' => 'Big',
                'Partner' => 'flybook',
                'OptResult' => 'packing,authors,fulldescription,Toc',
            ]
        ]);

        $response = json_decode($response->getBody()->getContents());

        if (isset($response->item)) {
            $item = $response->item[0];
            $book->author = $item->author;
            $book->publisher = $item->publisher;
            $book->pubdate = $item->pubDate;
            $book->description = $item->description;
            $book->category_id = $this->getCategoryId($item->categoryId, $item->categoryName);
            $book->standard_price = $item->priceStandard;
            $book->sales_price = $item->priceSales;
            $book->point = $item->mileage;
            $book->mall_type = $item->mallType;
            $book->stock_status = $item->stockStatus;
            $book->description_html = isset($item->fullDescription) ? $item->fullDescription : '';
            $book->description_html2 = isset($item->fullDescription2) ? $item->fullDescription2 : '';
            // echo "description_html2: ", $item->fullDescription2, "\n";
            if (isset($item->subInfo)) {

                if (isset($item->subInfo->authors) && count($item->subInfo->authors) > 0) {
                    $book->author = $item->subInfo->authors[0]->authorName;
                    $book->author_id = $item->subInfo->authors[0]->authorId;
                    $author_cnt = 0;
                    foreach ($item->subInfo->authors as $author) {
                        if ($author->authorType == 'author') $author_cnt++;
                    }

                    if ($author_cnt > 1) {
                        $book->author = $book->author . ' 외 ' . ($author_cnt - 1) . '명';
                    }
                }

                $book->toc = isset($item->subInfo->toc) ? $item->subInfo->toc : '';
                $book->page = isset($item->subInfo->itemPage) ? $item->subInfo->itemPage : 0;


                if (isset($item->subInfo->packing)) {
                    $model = BookPackingModel::where('book_id', '=', $book->id);
                    if (empty($model)) {
                        $book_packing = BookPackingModel::firstOrCreate([
                            'book_id' => $book->id,
                            'style_desc' => $item->subInfo->packing->styleDesc,
                            'weight' => $item->subInfo->packing->weight,
                            'size_depth' => $item->subInfo->packing->sizeDepth,
                            'size_height' => $item->subInfo->packing->sizeHeight,
                            'size_width' => $item->subInfo->packing->sizeWidth,
                        ]);
                    }
                }

                if (isset($item->subInfo->authors)) {
                    foreach ($item->subInfo->authors as $author) {

                        $tmp_model = AuthorModel::find($author->authorId);
                        if (!$tmp_model) {
                            //작가등록
                            AuthorModel::create([
                                'id' => $author->authorId,
                                'author_name' => $author->authorName,
                                'author_info' => $author->authorInfo,
                                'author_info_link' => $author->authorInfoLink,
                            ]);
                        }

                        $model = NULL;
                        $model = BookAuthorModel::where('author_id', '=', $author->authorId)->where('author_type', '=', $author->authorType);
                        //저자등록
                        if (!$model) {
                            BookAuthorModel::firstOrCreate([
                                'book_id' => $book->id,
                                'author_id' => $author->authorId,
                                'author_type' => $author->authorType,
                                'author_type_desc' => $author->authorTypeDesc,
                            ]);
                        }
                    }
                }
            }
            $book->is_data = 'Y';
        } else {
            $book->is_data = 'N';
        }

        $book->save();
    }

    public function makeBookShareImg($book): string
    {
        try {
            $img = Image::make('//cdnimg.flybook.kr/file/20180209172400_55429.jpg');
            if ($book->book_img) {
                $watermark = Image::make($book->book_img);
                $watermark->resize(150, $watermark->getSize()->height * 150 / $watermark->getSize()->width);
                $img->insert($watermark, 'bottom-right', 70, -30);
            }

            $path = 'book_share';
            $awsUploadService = new AWSUploadService();
            return config('image.images_url') . '/book_share/' . $awsUploadService->putImage($img->encode('data-url'), $path);

        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            return '//cdnimg.flybook.kr/file/20180209172400_55429.jpg';
        }
    }

    public function getCategoryId($categoryId, $categoryName)
    {
        if (!$categoryId) return 0;

        $category = CategoriesModel::find($categoryId);
        if (!$category) {
            $category_arr = explode(">", $categoryName);

            $category = new CategoriesModel;
            $category->id = $categoryId;
            $category->category_name = $category_arr[count($category_arr) - 1];
            if ($category_arr[0] == 'eBook') {
                $category->mall = '전자책';
            } else {
                $category->mall = $category_arr[0];
            }
            if ($category_arr[1] == '중고등참고서') {
                $category->depth1 = '고등학교참고서';
            } else {
                $category->depth1 = $category_arr[1];
            }
            $category->depth2 = isset($category_arr[2]) ? $category_arr[2] : '';
            $category->depth3 = isset($category_arr[3]) ? $category_arr[3] : '';
            $category->depth4 = isset($category_arr[4]) ? $category_arr[4] : '';
            $category->depth5 = isset($category_arr[5]) ? $category_arr[5] : '';
            $category->save();
        }

        /**
         **/
        $input_category = CategoriesModel::where('mall', $category->mall)->where('depth1', $category->depth1)->where('depth2', $category->depth2)->where('depth3', '')->first();

        if (!$input_category) {
            $input_category = CategoriesModel::where('mall', $category->mall)->where('depth1', $category->depth1)->where('depth2', '')->first();
        }

        if (!$input_category) {
            $category_id = 0;
        } else {
            $category_id = $input_category->id;
        }

        return $category_id;
    }
}
