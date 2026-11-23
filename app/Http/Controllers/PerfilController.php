<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFotoRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class PerfilController extends Controller
{
    public function edit(): View
    {
        return view('perfil.edit', ['user' => auth()->user()]);
    }

    public function updateFoto(UpdateFotoRequest $request): RedirectResponse
    {
        $this->salvarFoto($request->user(), $request->file('foto'));

        return redirect()
            ->route(auth()->user()->isAdmin() ? 'perfil.edit' : 'minha-escala')
            ->with('success', 'Foto atualizada.');
    }

    public function updateFotoAdmin(UpdateFotoRequest $request, User $user): RedirectResponse
    {
        $this->salvarFoto($user, $request->file('foto'));

        return redirect()
            ->route('admin.usuarios.edit', $user)
            ->with('success', "Foto de {$user->name} atualizada.");
    }

    private function salvarFoto(User $user, $upload): void
    {
        DB::transaction(function () use ($user, $upload) {
            // Remove arquivos anteriores
            if ($user->foto_path) {
                Storage::disk('public')->delete($user->foto_path);
            }
            if ($user->foto_thumb_path) {
                Storage::disk('public')->delete($user->foto_thumb_path);
            }

            // Salva o original no disco public (storage/app/public/fotos/originais)
            $originalPath = $upload->store('fotos/originais', 'public');

            // Gera a thumbnail WebP
            $thumbContent = $this->gerarThumbnailWebp($upload->getRealPath(), 200);
            $thumbPath = "fotos/thumbs/{$user->id}.webp";
            Storage::disk('public')->put($thumbPath, $thumbContent);

            $user->update([
                'foto_path' => $originalPath,
                'foto_thumb_path' => $thumbPath,
            ]);
        });
    }

    private function gerarThumbnailWebp(string $sourcePath, int $size): string
    {
        $data = @file_get_contents($sourcePath);
        if ($data === false) {
            throw new RuntimeException('Não foi possível ler o arquivo de imagem.');
        }

        $src = @imagecreatefromstring($data);
        if ($src === false) {
            throw new RuntimeException('Formato de imagem não suportado.');
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $lado = min($srcW, $srcH);
        $srcX = (int) (($srcW - $lado) / 2);
        $srcY = (int) (($srcH - $lado) / 2);

        $dst = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $lado, $lado);

        ob_start();
        imagewebp($dst, null, 80);
        $webp = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $webp;
    }
}