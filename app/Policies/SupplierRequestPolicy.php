<?php

namespace App\Policies;

use App\Models\SupplierRequest;
use App\Models\User;

class SupplierRequestPolicy
{
    /**
     * Only admin users can download supplier request PDFs.
     * These PDFs contain business-sensitive pricing and supplier data.
     */
    public function download(User $user, SupplierRequest $supplierRequest): bool
    {
        return $user->isAdmin();
    }
}
