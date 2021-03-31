<?php

namespace LaravelSupports\Views\Components\Member;


use FlyBookModels\Members\MemberModel;
use LaravelSupports\Views\Components\BaseComponent;

class MemberComponent extends BaseComponent
{
    protected string $view = 'member.member_component';

    public MemberModel $member;

    /**
     * Create a new component instance.
     *
     * @param MemberModel $member
     */
    public function __construct(MemberModel $member)
    {
        $this->member = $member;
    }
}
