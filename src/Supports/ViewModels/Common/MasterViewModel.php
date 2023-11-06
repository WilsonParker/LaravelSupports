<?php

namespace LaravelSupports\Supports\ViewModels\Common;

use LaravelSupports\Auth\AuthHelper;
use LaravelSupports\Database\Traits\TransactionTrait;
use LaravelSupports\Html\Traits\TagBuildTrait;
use LaravelSupports\Objects\ObjectHelper;

/**
 *
 * @author  WilsonParker
 * @added   2019-08-02
 * @updated 2019-08-26
 */
abstract class MasterViewModel extends BaseViewModel
{
    use TransactionTrait;
    use TagBuildTrait;

    public $model;

    // 생성자로 넘겨받은 값 입니다
    public $selectedMenu;

    // 공동으로 사용할 Model 객체 입니다
    public $hasSelectedMenu = false;
    // 선택된 메뉴 객체 입니다
    public $user;
    public $isLogin = false;
    // 로그인 된 유저 정보 입니다
    public $sideBar;
    public $favoritesBar;
    // 좌측 메뉴 객체 입니다
    public $requestUri;
    // 즐겨찾기 객체 입니다
    public $routeQuery;
    // 요청한 uri 값 입니다
    public $paginate;
    // 요청한 route 값 입니다
    public $condition;
    // 페이지 목록 수 입니다
    public $viewPath = "";
    // 검색에 쓰일 조건 배열 값 입니다
    public $path = "";
    // 이미지 저장 경로 prefix 값 입니다
    /**
     * 해당 문자가 포함된 함수들을 제외하여 View 로 전달합니다
     * 대소문자를 구분하지 않습니다
     *
     * @type
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected $ignoreContains = [
        "command", "init"
    ];
    // 이미지 저장 경로 suffix 값 입니다
    protected $args;
    // 이미지 저장 전체 경로 값 입니다
    protected $uploadPath = "";
    // 이미지 타입 (image.php 참고) 입니다
    protected string $tableType = "";

    /**
     * MasterViewModel 을 extends 할 경우 __construct 대신 init 을 override 해야됩니다
     *
     * @param array $args
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;
        // 페이지 목록 수의 기본값 입니다
        $this->paginate = config("pageLimit");
        // 이미지 저장 경로 prefix 의 기본값 입니다
        $this->viewPath = config("constants.images.image.viewPath");
        $this->init();
    }

    abstract protected function init();

    /*private function getMenu($routeQuery)
    {
        // 3 depth 에 해당하는 메뉴들의 uri 와 요청된 route 와 비교하여 선택된 메뉴를 찾아냅니다
        $menu = $this->threeDepthMenus->first(function ($item) use ($routeQuery) {
            return collect(explode("/", $routeQuery))->contains($item->getExplodedUri());
        });
        // 선택된 메뉴를 못 찾았을 경우 메뉴 중 첫번째를 선택합니다
//        if (!isset($menu))
//            $menu = $sidebar->threeDepthMenus[0];
        return $menu;
    }*/

    protected function onArrayBefore($viewModel)
    {
        $this->sideBar->init();
        $this->favoritesBar->init();
        $this->isLogin = AuthHelper::isLogin();
        $this->user = AuthHelper::getAuthUser();
        $this->selectedMenu = $this->sideBar->getMenu($this->routeQuery);
        $this->hasSelectedMenu = ObjectHelper::isNonEmpty($this->selectedMenu);
    }

}
