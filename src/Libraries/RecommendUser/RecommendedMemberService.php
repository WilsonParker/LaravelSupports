<?php


namespace LaravelSupports\Libraries\RecommendUser;

use App\Services\Membership\MembershipService;
use FlyBookModels\Members\MemberModel;
use FlyBookModels\Members\PlusMemberModel;
use FlyBookModels\Members\RecommendedMemberModel;
use LaravelSupports\Libraries\RecommendUser\Exceptions\AlreadyRecommendedException;
use LaravelSupports\Libraries\RecommendUser\Exceptions\DoNotSelfRecommendedException;
use LaravelSupports\Libraries\RecommendUser\Exceptions\NotFoundRecommendedException;
use LaravelSupports\Libraries\RecommendUser\Exceptions\NotMembershipRecommendedException;
use LaravelSupports\Libraries\RecommendUser\Exceptions\NotUsableRecommendedException;
use LaravelSupports\Libraries\RecommendUser\Exceptions\SubscriberRecommendedException;
use Throwable;

class RecommendedMemberService
{
    private $member;

    /**
     * RecommendedMemberService constructor.
     *
     * @param $member
     */
    public function __construct(MemberModel $member)
    {
        $this->member = $member;
    }

    /**
     * 추천인 등록 가능 여부를 체크한 후 등록을 합니다
     * 추천인 혜택을 제공 합니다
     *
     * @param $recommendCode
     * @param $priceModel
     * @param bool $throwException
     * @return void
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/06/24
     * @updated 2020/06/24
     */
    public function recommend($recommendCode, $priceModel, $throwException = false)
    {
        $recommendedPlusMember = PlusMemberModel::getInfoWithRecommendCode($recommendCode);
        $recommendedMember = $recommendedPlusMember->member;
        if ($this->isValidate($recommendedPlusMember, $priceModel, $throwException)) {
            $this->offerRecommendBenefit($recommendedMember);
            $this->addRecommendMember($recommendedMember->id);
        }
    }

    /**
     * 추천인 코드를 사용할 수 있는지 확인 합니다
     *
     * @param $recommendCode
     * @param $priceModel
     * @param bool $throwException
     * @return bool
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/06/25
     * @updated 2020/06/25
     */
    public function isValidateWithCode($recommendCode, $priceModel, $throwException = false): bool
    {
        $recommendedPlusMember = PlusMemberModel::getInfoWithRecommendCode($recommendCode);
        return $this->isValidate($recommendedPlusMember, $priceModel, $throwException);
    }

    /**
     * 추천인 코드를 사용할 수 있는지 확인 합니다
     * $throwException 에 따라 에러를 발생시킬지
     * 발생 시키지 않고 false 를 제공할지 결정합니다
     *
     * @param $recommendedPlusMember
     * @param $priceModel
     * @param bool $throwException
     * @return bool
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/06/23
     * @updated 2020/06/23
     */
    public function isValidate($recommendedPlusMember, $priceModel, $throwException = false): bool
    {
        try {
            // 이미 추천인을 등록했는지 여부
            $isAlreadyUsed = RecommendedMemberModel::isHaveBeenRecommendMember($this->member->id);
            throw_if($throwException && $isAlreadyUsed, new AlreadyRecommendedException());
            $recommendedMember = $recommendedPlusMember->member;
            // 본인을 추천할 수 없습니다
            throw_if($throwException && $recommendedPlusMember->recom_code == $this->member->plusMember->recom_code, new DoNotSelfRecommendedException());
            // 추천인이 존재하는지 확인
            throw_if($throwException && !isset($recommendedPlusMember) && !isset($recommendedMember), new NotFoundRecommendedException());
            // 추천인을 사용할 수 있는 상품 인지 확인
            throw_if($throwException && !$priceModel->isUsableRecommendCode(), new NotUsableRecommendedException());
            // 구독 방식 멤버십 회원일 경우 추천인 혜택 지급 불가
            throw_if($throwException && $recommendedMember->isSubscribe(), new SubscriberRecommendedException());
        } catch (\Throwable $e) {
            if ($throwException) {
                throw $e;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 추천인 코드 등록 혜택을 제공 합니다
     *
     * @param $recommendedMember
     * @return void
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/06/25
     * @updated 2020/06/25
     */
    public function offerRecommendBenefit($recommendedMember)
    {
        $service = new MembershipService($this->member);
        $service->addMembership('premium', 'months', 1);
        // 멤버십이 아닐 경우 에러 발생
        // throw_unless($recommendedMember->isMembership(), new NotMembershipRecommendedException());
        throw_unless(isset($recommendedMember->plusMember), new NotMembershipRecommendedException());
        $service->setMember($recommendedMember);
        $service->addMembership('premium', 'months', 1);
    }

    /**
     * 추천인 등록 내역을 저장합니다
     *
     * @param
     * @return void
     * @throws AlreadyRecommendedException
     * @author  WilsonParker
     * @added   2020/06/25
     * @updated 2020/06/25
     */
    public function addRecommendMember($recommendedMemberID)
    {
        RecommendedMemberModel::createModel($this->member->id, $recommendedMemberID);
    }

}
