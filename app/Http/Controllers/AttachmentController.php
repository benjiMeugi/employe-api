<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * @var Attachment
     */
    private Attachment $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new Attachment();
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
     * Ajoute une pièce jointe à une Absence précise — attachable_type et
     * attachable_id sont déterminés ici, jamais envoyés par le client.
     *
     * @param Request $request
     * @param int $id id de l'Absence concernée
     */
    public function storeForAbsence(Request $request, int $id)
    {
        \App\Models\Absence::findOrFail($id); // 404 propre si l'Absence n'existe pas
        return $this->storeForAttachable($request, 'absence', $id);
    }

    /**
     * Ajoute une pièce jointe à une AbsenceRequest précise.
     *
     * @param Request $request
     * @param int $id id de l'AbsenceRequest concernée
     */
    public function storeForAbsenceRequest(Request $request, int $id)
    {
        \App\Models\AbsenceRequest::findOrFail($id); // 404 propre si elle n'existe pas
        return $this->storeForAttachable($request, 'absence_request', $id);
    }

    /**
     * Logique commune aux deux méthodes ci-dessus — le client n'envoie
     * jamais que "file" et, en option, "document_type".
     */
    private function storeForAttachable(Request $request, string $attachableType, int $attachableId)
    {
        $validator = $this->repository->check($request, $this->repository->rules());
        if (true !== $validator) {
            return $validator;
        }

        $path = $request->file('file')->store('attachments'); // disque "local" par défaut, privé

        $attachment = Attachment::create([
            'file_reference' => $path,
            'document_type' => $request->input('document_type'),
            'attachable_type' => $attachableType,
            'attachable_id' => $attachableId,
        ]);

        return $this->respondOk($attachment);
    }

    /**
     * Update resource
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, int $id)
    {
        $validator = $this->repository->check($request, $this->repository->update_rules(), $id);
        if (true !== $validator) {
            return $validator;
        }

        return $this->respondOk($this->repository->update($request, $id));
    }

    /**
     * Download resource — sert le fichier physique, via une route contrôlée
     * plutôt qu'une URL publique devinable.
     *
     * @param int $id
     */
    public function download(int $id)
    {
        $attachment = Attachment::findOrFail($id);
        return Storage::disk('local')->download($attachment->file_reference);
    }

    /**
     * Delete resource — retire aussi le fichier physique du disque
     *
     * @param Request $request
     * @param int $id
     */
    public function delete(Request $request, int $id)
    {
        $attachment = Attachment::findOrFail($id);
        Storage::disk('local')->delete($attachment->file_reference);

        return $this->respondOk($this->repository->delete($request, $id));
    }
}
