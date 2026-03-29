<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerDocument;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerDocumentController extends Controller
{
    public function create(Request $request)
    {
        $partner = Partner::findOrFail($request->query('partner_id'));

        return view('partner-documents.create', compact('partner'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'document_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $partner = Partner::findOrFail($data['partner_id']);
        $uploadedFile = $request->file('file');

        $path = $uploadedFile->store("partners/{$partner->id}/documents", 'local');

        $document = PartnerDocument::create([
            'partner_id' => $partner->id,
            'title' => $data['title'],
            'category' => $data['category'],
            'document_date' => $data['document_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'file_path' => $path,
            'original_filename' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
        ]);

        ActivityLogger::log(
            subject: $partner,
            event: 'updated',
            entityType: 'partner',
            title: $partner->name,
            message: 'Dodan dokument "' . $document->title . '" za partnera "' . $partner->name . '".',
            newValues: [
                'document_title' => $document->title,
                'document_category' => $document->category,
                'document_filename' => $document->original_filename,
            ]
        );

        return redirect()
            ->route('partners.show', $partner)
            ->with('success', 'Dokument je uspješno dodan.');
    }

    public function view(PartnerDocument $partnerDocument)
    {
        abort_unless(Storage::disk('local')->exists($partnerDocument->file_path), 404);

        return response()->file(
            Storage::disk('local')->path($partnerDocument->file_path),
            [
                'Content-Type' => $partnerDocument->mime_type ?: 'application/octet-stream',
            ]
        );
    }

    public function download(PartnerDocument $partnerDocument)
    {
        abort_unless(Storage::disk('local')->exists($partnerDocument->file_path), 404);

        return Storage::disk('local')->download(
            $partnerDocument->file_path,
            $partnerDocument->original_filename
        );
    }

    public function destroy(PartnerDocument $partnerDocument)
    {
        $partner = $partnerDocument->partner;

        ActivityLogger::log(
            subject: $partner,
            event: 'updated',
            entityType: 'partner',
            title: $partner->name,
            message: 'Obrisan dokument "' . $partnerDocument->title . '" za partnera "' . $partner->name . '".',
            oldValues: [
                'document_title' => $partnerDocument->title,
                'document_category' => $partnerDocument->category,
                'document_filename' => $partnerDocument->original_filename,
            ]
        );

        if (Storage::disk('local')->exists($partnerDocument->file_path)) {
            Storage::disk('local')->delete($partnerDocument->file_path);
        }

        $partnerDocument->delete();

        return redirect()
            ->route('partners.show', $partner)
            ->with('success', 'Dokument je obrisan.');
    }
}