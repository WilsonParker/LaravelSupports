<?php


namespace LaravelSupports\Libraries\Book;


use FlyBookModels\Books\BookInformationModel;
use FlyBookModels\Books\BookModel;
use FlyBookModels\Books\LoanBookPaymentModel;
use FlyBookModels\Books\LoanPenaltyPaymentModel;
use FlyBookModels\Offline\OfflineStoreModel;
use LaravelSupports\Libraries\Supports\Objects\HasDataWithDefaultTrait;

class LoanPaymentDetailService
{
    use HasDataWithDefaultTrait;

    protected $member;
    protected $payment;
    protected $payType = [
        'kakao_pay' => '카카오 페이',
        'kakaopay' => '카카오 페이',
        'nice_pay' => '카드 결제',
        'nice' => '카드 결제',
        'point' => '포인트 결제',
    ];

    /**
     * LoanPaymentDetailService constructor.
     *
     * @param $member
     */
    public function __construct($member = null, $payment = null)
    {
        $this->member = $member;
        $this->payment = $payment;
    }

    public function getLoanList($status, $page = 10)
    {
        $list = LoanBookPaymentModel::paginateMemberLoanList($this->member, $status, $page);
        $list->getCollection()->transform(function ($item) use ($status) {
            return $this->bindPaymentInformation($item, $status);
        });

        /**
         * 현재 상태의 책 전체 수량
         *
         * @author  seul
         * @added   2021-02-01
         * @updated 2021-02-01
         */
        $collect = collect(['loan_total_book' => LoanBookPaymentModel::countMemberLoanList($this->member, $status)]);

        $list = $collect->merge($list);

        return $list;
    }

    protected function bindPaymentInformation($payment, $status)
    {
        $deliveryLoanableDays = (int)$this->member->getMemberBenefit('delivery_loanable_days');
        $loanableDays = (int)$this->member->getMemberBenefit('delivery_loanable_days');
        $scheduleService = new LoanScheduleService();
        $result = [
            'payment_id' => null,
            'date' => $payment->created_at->format('Y.m.d'),
            'loan_type' => $payment->loan_type,
            'is_missing' => false,
            'loan_type_text' => null,
            'loan_status_text' => null,
            'return_status_text' => null,
            'penalty_status_text' => null,
            'delivery_loanable_days' => $deliveryLoanableDays,
            'loanable_days' => $loanableDays,
            'scheduled_delivery_date' => null,
            'scheduled_return_date' => null,
            'loan_date' => null,
            'return_date' => null,
            'overdue_date' => null,
            'goods' => []
        ];
        switch ($payment->loan_type) {
            case 'offline':
                $store = OfflineStoreModel::find($payment->ref_loaned_offline_store_id);
                $bookInfo = BookInformationModel::find($payment->ref_book_information_id);
                $book = BookModel::where([
                    ['isbn', $bookInfo->isbn],
                    ['book_img', $bookInfo->image_url]
                ])->first();
                if (!$book) {
                    $book = BookModel::where('isbn', $bookInfo->isbn)->orderBy('id', 'desc')->first();
                }
                $result['loan_type_text'] = is_null($store) ? '직접 대여' : $store->name . ' 직접 대여';
                $result['scheduled_return_date'] = $payment->scheduled_return_date ? $scheduleService->convertScheduledDeliveryDate(new \DateTime($payment->scheduled_return_date)) : null;
                $result['loan_date'] = $payment->loan_date ? $scheduleService->convertScheduledDeliveryDate(new \DateTime($payment->loan_date)) : null;
                $result['return_date'] = $payment->return_date ? $scheduleService->convertScheduledDeliveryDate(new \DateTime($payment->return_date)) : null;

                $overDueDate = !$payment->return_date ? (int)$scheduleService->bindOverDueDate(new \DateTime($payment->scheduled_return_date)) : 0;
                $result['overdue_date'] = $overDueDate > 0 ? $overDueDate . '일' : null;
                $result['goods'] = [
                    [
                        'status' => $payment->status,
                        'book_id' => $book->id,
                        'title' => $book->title,
                        'book_img' => $book->book_img,
                        'author' => $book->author,
                        'publisher' => $book->publisher,
                    ],
                ];
                break;
            case 'online':
            default:
                $result['penalty_status_text'] = $status == 'returned' ? $this->getPenaltyStatusText($payment) : null;
                $result['payment_id'] = $payment->id;
                $scheduleService = new LoanScheduleService();
                $result['loan_type_text'] = '집에서 받아보기';
                $result['scheduled_delivery_date'] = $scheduleService->getScheduledDeliveryDate($payment->created_at);
                $goods = $payment->goods()->with(['book', 'delivery'])->get();

                $countLoanedBook = $goods->filter(function ($item) {
                    $history = $item->history;
                    return $history == null || in_array($history->status, ['loaned', 'overdue']);
                })->count();

                if ($status != 'ready') {
                    if ($countLoanedBook) {
                        if ($countLoanedBook == count($goods)) {
                            $result['loan_status_text'] = '반납 신청하기';
                        } else {
                            $result['is_missing'] = true;
                            $result['loan_status_text'] = "미반납 도서 ({$countLoanedBook}권) 반납 신청하기";
                        }
                    }

                    $returnBook = $goods->filter(function ($item) {
                        $history = $item->history;
                        return $history != null && in_array($history->status, ['pickup']);
                    });

                    if ($returnBook->count()) {
                        $countDoesntHavePickupDate = $returnBook->filter(function ($item) {
                            $history = $item->history;
                            return $history->pickup_date == null;
                        })->count();
                        if ($countDoesntHavePickupDate != count($returnBook)) {
                            if ($countDoesntHavePickupDate > 0) {
                                $result['return_status_text'] = $countDoesntHavePickupDate . '권 방문 예정 / 도서 상태 확인중';
                            } else {
                                $result['return_status_text'] = '도서 상태 확인중';
                            }
                        } else {
                            $result['return_status_text'] = '방문 예정';
                        }
                    }
                }

                $overDue = [
                    'date' => null,
                    'price' => 0
                ];
                $result['goods'] = $goods->map(function ($item) use ($status, $scheduleService, &$result, &$overDue) {
                    $book = $item->book;
                    $history = $item->history;
                    if (!is_null($history)) {
                        if (is_null($result['scheduled_return_date']) && !is_null($history->scheduled_return_date)) {
                            $result['scheduled_return_date'] = !is_null($history->scheduled_return_date) ? $scheduleService->convertScheduledDeliveryDate($history->scheduled_return_date) : null;
                        }

                        if (is_null($result['return_date']) && !is_null($history->return_date)) {
                            $result['return_date'] = !is_null($history->return_date) ? $scheduleService->convertScheduledDeliveryDate($history->return_date) : null;
                        }

                        if (is_null($result['loan_date']) && !is_null($history->loan_date)) {
                            $result['loan_date'] = !is_null($history->loan_date) ? $scheduleService->convertScheduledDeliveryDate($history->loan_date) : null;
                        }

                        $overDueDate = $scheduleService->bindOverDueDate($history->scheduled_return_date, $history->return_date);
                        if ($overDueDate > 0) {
                            if (is_null($overDue['date'])) {
                                $overDue['date'] = $overDueDate;
                            }

                            if (in_array($history->status, ['loaned', 'overdue', 'pickup'])) {
                                $overDue['price'] += $overDueDate * 1000;
                            } else if ($history->status == 'returned') {
                                $hasOverDuePenalty = LoanPenaltyPaymentModel::where([
                                    'ref_loan_payment_id' => $item->ref_payment_id,
                                    'status' => 'paid'
                                ])->whereHas('goods', function ($query) {
                                    $query->where([
                                        'status' => 'paid',
                                        'type' => 'overdue',
                                    ]);
                                })->exists();

                                $overDue['price'] = $hasOverDuePenalty ? 0 : $overDue['price'];
                            }
                        }
                    }


                    if ($overDue['price'] > 0) {
                        $result['overdue_date'] = is_null($overDue['date']) ? null : $overDue['date'] . '일';
                        $result['overdue_date'] .= '(' . number_format($overDue['price']) . '원)';
                    }


                    return [
                        'status' => !is_null($history) ? $history->status : $status,
                        'book_id' => $book->id,
                        'title' => $book->title,
                        'book_img' => $book->book_img,
                        'author' => $book->author,
                        'publisher' => $book->publisher,
                    ];
                });
                break;
        }

        return $result;
    }

    public function getPenaltyStatusText($payment)
    {
        $text = '';
        $types = [
            'overdue' => '연체료',
            'damaged' => '파손비용',
            'delivery' => '수거비용',
        ];

        $hasType = function ($payments, $type) {
            return $payments->filter(function ($payment) use ($type) {
                return $payment->goods()->where([
                    ['status', 'ready'],
                    ['type', $type]
                ])->exists();
            });
        };

        $penaltyPayments = $payment->wherePenaltyStatus('ready')->get();

        if (count($penaltyPayments)) {
            foreach ($types as $key => $typeText) {
                if (count($hasType($penaltyPayments, $key))) {
                    if ($text != '') {
                        $text .= '/';
                    }

                    $text .= $typeText;
                }
            }

            return $text == '' ? '결제하기' : $text . ' 결제하기';
        } else {
            return null;
        }
    }

    public function getDetailInformation(LoanBookPaymentModel $payment)
    {
        $this->payment = $payment;

        $hasLoanedGoods = $payment->goods()->whereHas('history', function ($query) {
            $query->where('status', '!=', 'returned');
        })->exists();
        $isNotCancelable = $payment->goods()->where('status', '!=', 'paid')->exists();


        $loanedGood = $payment->goods()->first();
        $history = $loanedGood->history;
        $delivery = $loanedGood->delivery;

        $useFree = $payment->goods()->where('total_price', 0)->count();

        $paymentModuleCode = $payment->pay_amount > 0 ? $payment->ref_payment_module_code : 'point';
        $loanCost = 3000;

        $overdueDate = null;
        if ($history) {
            $scheduleService = new LoanScheduleService();
            $overdueDate = $scheduleService->bindOverDueDate($history->scheduled_return_date, $history->return_date);
        }
        return [
            'loan_cost' => $loanCost,
            'is_cancelable' => !$isNotCancelable,
            'order' => [
                'order_no' => $payment->order_no,
                'paid_at' => $payment->paid_at->format('Y년 m월 d일 H시 i분'),
                'loan_date' => $history && $history->loan_date ? $history->loan_date->format('Y년 m월 d일') : null,
                'return_date' => $history && $history->return_date ? $history->return_date->format('Y년 m월 d일') : null,
                'overdue_date' => $overdueDate,
            ],
            'goods' => $this->getGoodsInformation(),
            'delivery' => [
                'title' => '책이 문 앞에 도착했어요!',
                'user_name' => $delivery->name,
                'user_contact' => $delivery->phone,
                'delivery_img' => $history ? $history->delivery_img : '',
                'address' => $delivery->getAddress(),
                'door_password' => $delivery->door_password ? decrypt($delivery->door_password) : null,
                'message' => $delivery->message,
                'delivery_date' => $history && $history->loan_date ? $scheduleService->convertScheduledDeliveryDate($history->loan_date, 'Y년 m월 d일 A h시 i분', false) : null,
            ],
            'payment' => [
                'total_price' => $payment->goods()->count() * $loanCost,
                'delivery_cost' => $delivery->delivery_cost,
                'use_point' => $payment->use_point,
                'use_free' => $useFree,
                'use_free_price' => $useFree * $loanCost,
                'pay_amount' => $payment->pay_amount,
                'pay_type' => $this->getArrayDataWithDefault($this->payType, $paymentModuleCode, '기타'),
            ],
            'cancel' => [
                'cancel_amount' => !is_null($payment->cancelled_at) ? $payment->cancel_amount : null,
                'cancel_point' => !is_null($payment->cancelled_at) ? $payment->cancel_point : null,
                'cancelled_at' => !is_null($payment->cancelled_at) ? $payment->cancelled_at->format('Y년 m월 d일 H시 i분') : null,
            ],
            'penalty' => $this->getPenaltyPaymentsInformation(),
        ];
    }

    protected function getPenaltyPaymentsInformation()
    {
        $penaltyPayments = $this->payment->wherePenaltyStatus('paid')->with('goods')->get();
        $penaltyPaymentModuleCode = null;
        $hasPayments = count($penaltyPayments);
        $totalPrice = 0;
        if ($hasPayments) {
            $firstPayment = $penaltyPayments->first();
            $penaltyPaymentModuleCode = $firstPayment->pay_amount > 0 ? $firstPayment->ref_payment_module_code : 'point';

            $totalPrice = $penaltyPayments->map(function ($item) {
                return $item->goods()->where('status', 'paid')->sum('total_price');
            })->sum();
        }

        $sumPayments = function ($payments, $column) {
            return $payments->sum($column);
        };

        $sumGoodsPayments = function ($payments, $type) {
            return $payments->map(function ($payment) use ($type) {
                return $payment->goods()->where([
                    'status' => 'paid',
                    'type' => $type
                ])->sum('total_price');
            })->sum();
        };

        return [
            'total_price' => $hasPayments ? $totalPrice : null,
            'overdue_price' => $hasPayments ? $sumGoodsPayments($penaltyPayments, 'overdue') : null,
            'damaged_price' => $hasPayments ? $sumGoodsPayments($penaltyPayments, 'damaged') : null,
            'delivery_price' => $hasPayments ? $sumGoodsPayments($penaltyPayments, 'delivery') : null,
            'etc_price' => $hasPayments ? $sumGoodsPayments($penaltyPayments, 'etc') : null,
            'use_point' => $hasPayments ? $sumPayments($penaltyPayments, 'use_point') : null,
            'pay_amount' => $hasPayments ? $sumPayments($penaltyPayments, 'pay_amount') : null,
            'pay_type' => $hasPayments ? $this->getArrayDataWithDefault($this->payType, $penaltyPaymentModuleCode, '기타') : null,
        ];
    }

    protected function getGoodsInformation()
    {
        return $this->payment->goods()->with(['book'])->get()->map(function ($item) {
            $book = $item->book;
            return [
                'book_id' => $book->id,
                'title' => $book->title,
                'book_img' => $book->book_img,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'total_price' => $item->total_price,
            ];
        });
    }

    public function getPaymentInformation($payment, $status, $loanType = 'online')
    {
        $payment->loan_type = $loanType;
        return $this->bindPaymentInformation($payment, $status);
    }
}
