<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * @var Promotion
     */
    private Promotion $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new Promotion();
        $this->repository = new Repository($this->model);
    }

    /**
     * List resource
     *
     * @param Request $request
     */
    public function index(Request $request, $id = null)
    {
        if ($id !== null) {
            return $this->show($request, $id);
        }

        $query = $this->repository->parse_filters($request);

        if ($request->has('page') && $request->has('per_page')) {
            return $this->respondOk($query->paginate($request->input('per_page')));
        }
        return $this->respondOk($query->get());
    }

    /**
     * List single resource
     *
     * @param Request $request
     * @param int $id
     */
    public function show(Request $request, int $id)
    {
        return $this->repository->show($request, $id);
    }

    /**
     * Store resource — crée la ligne career_events ET promotions ensemble
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        return $this->respondOk($this->repository->store($request));
    }

    /**
     * Update resource
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, int $id)
    {
        return $this->respondOk($this->repository->update($request, $id));
    }

    /**
     * Delete resource — supprime via career_events (cascade vers promotions)
     *
     * @param Request $request
     * @param int $id
     */
    public function delete(Request $request, int $id)
    {
        return $this->respondOk($this->repository->delete($request, $id));
    }
}
