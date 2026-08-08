<?php

namespace App\Interfaces;

interface PeerToPeerRepositoryInterface
{
    public function sendAppraisalForm($peertopeer,array $request);
}
