<?php

namespace App\Model\Security\Auth;

class PermissionSection
{
    public function __construct(private string $section, private string $readNode, private string $editNode) {
    }

    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * @return string
     */
    public function getReadNode(): string
    {
        return $this->readNode;
    }

    /**
     * @return string
     */
    public function getEditNode(): string
    {
        return $this->editNode;
    }
}