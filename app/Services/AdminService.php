<?php

namespace App\Services;

use App\Repositories\Admin\AdminRepository;

class AdminService
{
    public function __construct(
        protected AdminRepository $adminRepository
    )
    {}

    public function dashboard()
    {
        $data = $this->adminRepository->dashboard();
        return [
            'products' => $data[0],
            'orders'   => $data[1],
            'revenue'  => $data[2],
        ];
    }
}