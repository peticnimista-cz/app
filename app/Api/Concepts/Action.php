<?php

namespace App\Model\API;

interface Action
{
    const GET = "GET";

    // also History log types
    const POST = "POST";
    const PATCH = "PATCH";
    const DELETE = "DELETE";
}