<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerShowcaseController extends Controller
{
    public function preview(Request $request)
    {
        $partners = [
            ['name' => 'BPOM RI', 'logo' => '/images/partners/bpom.png', 'url' => 'https://www.pom.go.id/'],
            ['name' => 'MUI Halal', 'logo' => '/images/partners/mui.png', 'url' => 'https://halalmui.org/'],
            ['name' => 'WHO', 'logo' => '/images/partners/who.png', 'url' => 'https://www.who.int/'],
            ['name' => 'OpenFoodFacts', 'logo' => '/images/partners/off.png', 'url' => 'https://world.openfoodfacts.org/'],
            ['name' => 'OpenFDA', 'logo' => '/images/partners/fda.png', 'url' => 'https://open.fda.gov/'],
            ['name' => 'Campus Partner', 'logo' => '/images/partners/campus.png', 'url' => '#'],
        ];

        return view('admin.partners.index', compact('partners'));
    }
}
