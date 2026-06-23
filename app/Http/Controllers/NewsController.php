<?php

namespace App\Http\Controllers;

use App\Services\NewsAggregatorService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    function __construct(private NewsAggregatorService $newsService) {}
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->newsService->fetchAll('technology', 1);
    }
}
