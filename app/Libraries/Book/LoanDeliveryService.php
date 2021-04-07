<?php


namespace LaravelSupports\Libraries\Book;


use App\Events\Notifications\LoanDeliveryRestockNotificationEvent;
use App\Services\Notifications\AlimtalkService;
use App\Services\Push\FCMPushService;
use App\Services\Push\PushGroupService;
use Carbon\Carbon;
use FlyBookModels\Books\HopeBookModel;
use FlyBookModels\Books\LoanBookPaymentGoodsModel;
use FlyBookModels\Books\LoanBookPaymentModel;
use FlyBookModels\Books\LoanDeliveryHistoryModel;
use FlyBookModels\Delivery\DeliveryModel;
use FlyBookModels\Members\MemberModel;
use FlyBookModels\Offline\OfflineLoanBookModel;
use FlyBookModels\Push\AlimGroupModel;
use FlyBookModels\Push\MemberAlimModel;
use GuzzleHttp\Client;
use LaravelSupports\Libraries\Payment\Exceptions\GoodStatusException;

class LoanDeliveryService
{
    public $lateFee = 1000;
    public $pickupCost = 3000;

    public function updateDeliveryPush($payments)
    {
        $payments->filter(function ($payment) {
            return !$payment->histories;
        })->each(function ($payment) {
            $payment->goods->each(function ($good) {
                if (in_array($good->status, ['paid', 'preparing', 'ordered'])) {
                    $good->update(['status' => 'shipping']);
                    $this->storeHistory($good);
                }
            });
        });

        $deliveries = LoanDeliveryHistoryModel::where('status', 'ready')->with('good')->get();
        $members = $deliveries->map(function ($item) {
            $payment = $item->good->payment;
            $book = $item->good->book;

            return [
                'payment_id' => $payment->id,
                'member_id' => $payment->ref_member_id,
                'book_title' => $book->title,
            ];
        })->groupBy('payment_id');

        $members->each(function ($item, $paymentID) {
            $info = $item[0];
            $member = MemberModel::find($info['member_id']);
            if ($member) {
                $bookCount = count($item) - 1;

                $bookName = mb_strlen($info['book_title']) > 10 ? mb_substr($info['book_title'], 0, 10) . "..." : $info['book_title'];
                if ($bookCount) {
                    $bookName .= " 외 {$bookCount}권";
                }

                $message = $member->nickname."님, 대여한 책 ({$bookName}) 이 오늘 도착 예정입니다!";
                $data = [
                    'target' => 'basic',
                    'use_push' => 'Y',
                    'message' => $message,
                    'page' => 'LoanDetailPage',
                    'page_idx' => $paymentID
                ];

                $alimGroup = AlimGroupModel::create($data);

                $service = new PushGroupService($alimGroup, [$member->id]);
                $service->sendNotifications();
            }
        });
    }

    protected function storeHistory($good, String $status = 'ready')
    {
        $history = LoanDeliveryHistoryModel::firstOrCreate([
            'ref_delivery_rider_id' => 1,
            'ref_good_id' => $good->id,
        ]);

        switch ($status) {
            case 'return':
                $history->status = 'returned';
                $history->loan_date = is_null($history->loan_date) ? now() : $history->loan_date;
                $history->scheduled_pickup_date = is_null($history->scheduled_pickup_date) ? now() : $history->scheduled_pickup_date;
                $history->pickup_date = is_null($history->pickup_date) ? now() : $history->pickup_date;
                $history->return_date = now();

                $book = $good->book;

                $offlineStock = OfflineLoanBookModel::findStock($book->id, 26);
                if ($offlineStock && $offlineStock->loaned_quantity > 0) {
                    $offlineStock->decrement('loaned_quantity', 1);
                    $offlineStock->increment('remained_quantity', 1);
                }

                $targetMemberIDs = HopeBookModel::where('ref_book_id', $book->id)->get()->pluck('ref_member_id')->toArray();
                if (!empty($targetMemberIDs)) {
                    event(new LoanDeliveryRestockNotificationEvent($targetMemberIDs, ['book' => $book]));
                }
                break;
            case 'pickup_done':
                $history->status = 'pickup';
                $history->loan_date = is_null($history->loan_date) ? now() : $history->loan_date;
                $history->scheduled_pickup_date = is_null($history->scheduled_pickup_date) ? now() : $history->scheduled_pickup_date;
                $history->pickup_date = now();
                break;
            case 'pickup_ready':
                $history->status = 'pickup';
                $history->loan_date = is_null($history->loan_date) ? now() : $history->loan_date;
                $history->scheduled_pickup_date = now();
                break;
            case 'shipping':
                $history->status = 'loaned';
                $history->loan_date = now();
                $history->scheduled_return_date = now()->addDays(15);
                break;
            case 'ready':
                $history->status = is_null($history->status) ? 'ready' : $history->status;
                $history->loan_date = now();
                $history->scheduled_return_date = now()->addDays(15);
                break;
        }

        $history->save();
    }

    public function updateDeliveryStatus()
    {
        $apiURL = 'https://partner.baroquick.kr/api/deliveries';

        $client = new Client();
        $response = $client->request('GET', $apiURL, [
            'headers' => [
                'x-access-token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6NDIwLCJ1c2VybmFtZSI6ImZseWJvb2siLCJjb3JwVGl0bGUiOiLtlIzrnbzsnbTrtoEiLCJyZWFsbmFtZSI6Iu2UjOudvOydtOu2gSIsImFwaUtleSI6bnVsbCwiZW1haWwiOiIiLCJjb250YWN0IjoiMDcwNTAyOTE0MjIiLCJtYWNBZGRyZXNzIjpudWxsLCJsZXZlbCI6Im5vcm1hbCIsInR5cGUiOiJOT1JNQUwiLCJhdXRvUHJpbnQiOnRydWUsImxvZ29JbWFnZSI6bnVsbCwiZGVsZXRlZCI6ZmFsc2UsInNwb3RzIjpbeyJpZCI6MjY2LCJ0dXJuIjpudWxsLCJuYW1lIjoi7ZSM65287J2067aBIiwiY29udGFjdCI6IjAxMDk2OTY3MDE3IiwiY29udGFjdDIiOiIiLCJhZGRyZXNzIjoi7ISc7Jq4IOuniO2PrOq1rCDsl7Drgqjrj5kgNTA0LTjrsojsp4AiLCJhZGRyZXNzUm9hZCI6IuyEnOyauCDrp4jtj6zqtawg7ISx66-47IKw66GcIDExNi01IiwiYWRkcmVzc0RldGFpbCI6IjHsuLUiLCJwb3N0YWxDb2RlIjpudWxsLCJwb2ludCI6eyJ0eXBlIjoiUG9pbnQiLCJjb29yZGluYXRlcyI6WzEyNi45MjAyMDI1OTE1LDM3LjU2MjIzNjU2ODJdfSwiY29kZSI6IjEwMjQwIiwiZmVlIjo5OTk5LCJtZW1vIjpudWxsLCJleGNlbFNjaGVtZSI6IntcInR5cGVcIjpcIuycoO2YlVwiLFwiY3VzdG9tZXJOYW1lXCI6XCLrsJvripTrtoRcIixcImN1c3RvbWVyTW9iaWxlXCI6XCLrsJvripTrtoTsoITtmZTrsojtmLhcIixcImN1c3RvbWVyQWRkcmVzc1wiOlwi67Cb64qU67aE7KO87IaMKOyghOyytCzrtoTtlaApXCIsXCJwcmVwYWlkXCI6XCLshKDrtohcIixcInByb2R1Y3ROYW1lXCI6XCLrgrTtkojrqoVcIixcInByb2R1Y3RDYXRlZ29yeVwiOlwi7KCc7ZKI7KKF66WYXCIsXCJwcm9kdWN0UHJpY2VcIjpcIuygnO2SiOqwgOqyqVwiLFwicHJvZHVjdENvdW50XCI6XCLrgrTtkojsiJhcIixcIm1lbW9Gcm9tQ3VzdG9tZXJcIjpcIuuwsOyGoeuplOyEuOyngDFcIixcIm9yZGVySWRGcm9tQ29ycFwiOlwi7KO866y467KI7Zi4XCIsXCJldGMxXCI6XCLquLDtg4Dsgqztla0xXCIsXCJldGMyXCI6XCLquLDtg4Dsgqztla0yXCIsXCJldGMzXCI6XCLquLDtg4Dsgqztla0zXCIsXCJkb29yS2V5XCI6XCLqs7Xrj5ntmITqtIAg67mE67CA67KI7Zi4XCJ9Iiwic21zQWN0aXZlIjpmYWxzZSwicmV0dXJuQWN0aXZlIjpmYWxzZSwicmV0dXJuRW5hYmxlZCI6Ik5PTkUiLCJnYXRoZXJpbmdSZWZlcmVuY2UiOiJERUZBVUxUIiwiZG9uZ0lkIjoxMDksIkNvcnBVc2VyU3BvdCI6eyJjb3JwVXNlcklkIjo0MjAsInNwb3RJZCI6MjY2fX1dLCJpYXQiOjE2MTU1MzEzNzR9.VFVCk2jb5VZlbroY_Qu8tfTjp_jeGJSkiObxbHfZ69I'
            ],
            'query' => [
                'sortKey' => 'receiptDate',
                'sortType' => 'DESC',
                'dateFrom' => date('Y-m-d'),
                'dateTo' => date('Y-m-d'),
                'status' => 'all',
                'query' => '',
                'filterByLevel' => 'only-me',
            ],
        ]);

        $contents = json_decode($response->getBody()->getContents(), true);

        collect($contents)->each(function ($item) {
            $orderNo = $item['orderIdFromCorp'];
            $deliveryNum = $item['bookId'];
            $image = $item['notReceivedImageLocation'];
            $loanDate = $item['deliveryCompletedDate'];
            $released = $item['releasedAt'];
            $pickupDate = $item['pickupDateCompleted'];

            if (is_null($orderNo)) {
                if ($pickupDate) {
                    $deliveryNum = mb_substr($deliveryNum, 1, 10);

                    $delivery = DeliveryModel::where('delivery_num', $deliveryNum)->first();
                    if ($delivery) {
                        $goods = LoanBookPaymentGoodsModel::where('ref_delivery_id', $delivery->id)
                            ->whereIn('status', ['shipping'])
                            ->with('payment', 'book', 'history')
                            ->get();

                        $good = $goods->first();
                        $payment = $good->payment;
                        $member = $payment->member;
                        $book = $good->book;

                        $cnt = 0;
                        foreach ($goods as $good) {
                            $history = $good->history;
                            if ($history->status == 'pickup' && is_null($history->pickup_date)) {
                                $history->pickup_date = $pickupDate ? Carbon::create($pickupDate)->addHours(9) : null;
                                $history->save();

                                $cnt++;
                            }
                        }

                        if ($cnt > 0) {
                            $bookName = mb_strlen($book->title) > 10 ? mb_substr($book->title, 0, 10) . "..." : $book->title;
                            $bookCount = $cnt - 1;
                            if ($bookCount) {
                                $bookName .= " 외 {$bookCount}권";
                            }

                            $message = "대여하신 책 ({$bookName})을 수거 완료했습니다. 책 상태 확인 후 반납이 완료됩니다!";
                            $data = [
                                'category' => 'etc',
                                'member_id' => '2234',
                                'target_id' => $member->id,
                                'message' => $message,
                                'page' => 'LoanedPage',
                                'page_idx' => 0,
                            ];

                            MemberAlimModel::create($data);

                            $service = new FCMPushService();
                            $service->pushAll(collect([$member]), $data['message'], $data['page'], $data['page_idx']);
                        }
                    }
                }
            } else {
                $payment = LoanBookPaymentModel::where('order_no', $orderNo)->first();
                $member = $payment->member->load('device');

                if ($payment) {
                    $isNewImage = false;
                    $isNewReleased = false;
                    foreach ($payment->goods()->with('history')->get() as $good) {
                        $delivery = $good->delivery;
                        $history = $good->history;

                        if (is_null($delivery->delivery_num)) {
                            $delivery->delivery_num = $deliveryNum;
                            $delivery->save();
                        }

                        if ($history->status == 'ready' && $released) {
                            $isNewReleased = true;
                            $history->status = 'loaned';
                        }

                        if (is_null($history->delivery_img) && $image) {
                            $isNewImage = true;
                            $history->delivery_img = $image;
                            $history->loan_date = $loanDate ? Carbon::create($loanDate)->addHours(9) : null;

                            $history->status = 'loaned';
                        }

                        $history->save();
                    }

                    if ($isNewReleased) {
                        $data = [
                            'phone' => $delivery->phone,
                            'name' => $delivery->name,
                            'book_title' => $payment->description,
                            'scheduled_received_time' => '4시간 이내'
                        ];
                        $service = new AlimtalkService('ldv1', $data);
                        $service->send();
                    }

                    if ($isNewImage) {
                        $message = "문앞에 책이 도착했어요! 얼른 책을 챙겨주세요.";
                        $data = [
                            'category' => 'etc',
                            'member_id' => '2234',
                            'target_id' => $member->id,
                            'message' => $message,
                            'page' => 'LoanDetailPage',
                            'page_idx' => $payment->id,
                        ];

                        MemberAlimModel::create($data);

                        $service = new FCMPushService();
                        $service->pushAll(collect([$member]), $data['message'], $data['page'], $data['page_idx'], '', $image);
                    }
                }
            }
        });
    }

    public function updatePickupStatus($payments)
    {
        $deliveries = $payments->map(function ($item) {
            return $item->goods()->whereHas('history', function ($query) {
                $query->where('status', 'pickup')->whereNull('pickup_date');
            })->with('payment', 'book')->get();
        })->flatten(1);

        $members = $deliveries->map(function ($item) {
            $payment = $item->payment;
            $book = $item->book;

            return [
                'member_id' => $payment->ref_member_id,
                'book_title' => $book->title,
            ];
        })->groupBy('member_id');

        $members->each(function ($item, $key) {
            $member = MemberModel::find($key);
            if ($member) {
                $info = $item[0];
                $bookCount = count($item) - 1;

                $bookName = mb_strlen($info['book_title']) > 10 ? mb_substr($info['book_title'], 0, 10) . "..." : $info['book_title'];
                if ($bookCount) {
                    $bookName .= " 외 {$bookCount}권";
                }

                $message = "대여하신 책 ({$bookName})을 오늘 오후에 플라이북에서 수거 예정입니다!";
                $data = [
                    'target' => 'basic',
                    'use_push' => 'Y',
                    'message' => $message,
                    'page' => 'LoanedPage',
                    'page_idx' => 0,
                ];

                $alimGroup = AlimGroupModel::create($data);

                $service = new PushGroupService($alimGroup, [$member->id]);
                $service->sendNotifications();
            }
        });
    }

    public function updateGoodsStatus($data)
    {
        $goodIDs = $data['good_ids'];
        $status = $data['status'];

        $goods = LoanBookPaymentGoodsModel::getModelWhereIn($goodIDs);

        $ignore = $goods->filter(function ($item) use ($status) {
            return $item->isIgnoreStatus($status);
        });

        throw_if(!$ignore->isEmpty(), new GoodStatusException());

        $isNew = true;
        foreach ($goods as $good) {
            $this->storeHistory($good ,$status);
            $good->update(['status' => 'shipping']);

            if ($isNew) {
                $payment = $good->payment;
                $goods = $payment->goods()->where('status', 'shipping')->get();
                $member = $payment->member;
                $book = $good->book;

                $bookName = mb_strlen($book->title) > 10 ? mb_substr($book->title, 0, 10) . "..." : $book->title;
                $bookCount = count($goods) - 1;
                if ($bookCount) {
                    $bookName .= " 외 {$bookCount}권";
                }

                switch ($status) {
                    case 'return':
                        $message = "대여한 책 ({$bookName})이 반납 완료되었습니다. 책은 어떠셨나요? 리뷰를 남겨주세요!";
                        $data = [
                            'category' => 'etc',
                            'member_id' => '2234',
                            'target_id' => $member->id,
                            'message' => $message,
                            'page' => 'BookDetailPage',
                            'page_idx' => $book->id,
                        ];

                        MemberAlimModel::create($data);

                        $service = new FCMPushService();
                        $service->pushAll(collect([$member]), $data['message'], $data['page'], $data['page_idx']);
                        break;
                    case 'pickup_done':
                        $message = "대여하신 책 ({$bookName})을 수거 완료했습니다. 책 상태 확인 후 반납이 완료됩니다!";
                        $data = [
                            'category' => 'etc',
                            'member_id' => '2234',
                            'target_id' => $member->id,
                            'message' => $message,
                            'page' => 'LoanedPage',
                            'page_idx' => 0,
                        ];

                        MemberAlimModel::create($data);

                        $service = new FCMPushService();
                        $service->pushAll(collect([$member]), $data['message'], $data['page'], $data['page_idx']);
                        break;
                }

                $isNew = false;
            }

        }
    }
}
