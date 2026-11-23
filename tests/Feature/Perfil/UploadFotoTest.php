<?php

namespace Tests\Feature\Perfil;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class UploadFotoTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_colaborador_faz_upload_da_propria_foto(): void
    {
        Storage::fake('public');

        $porteiro = $this->porteiro();

        $this->actingAs($porteiro)
            ->post(route('perfil.foto.update'), [
                'foto' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
            ])
            ->assertRedirect(route('minha-escala'));

        $porteiro->refresh();

        $this->assertNotNull($porteiro->foto_path);
        $this->assertNotNull($porteiro->foto_thumb_path);
        Storage::disk('public')->assertExists($porteiro->foto_path);
        Storage::disk('public')->assertExists($porteiro->foto_thumb_path);
    }

    public function test_thumbnail_e_gerada_em_webp_com_200x200(): void
    {
        Storage::fake('public');

        $porteiro = $this->porteiro();

        $this->actingAs($porteiro)->post(route('perfil.foto.update'), [
            'foto' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
        ]);

        $porteiro->refresh();
        $path = Storage::disk('public')->path($porteiro->foto_thumb_path);

        $this->assertStringEndsWith('.webp', $porteiro->foto_thumb_path);

        [$width, $height] = getimagesize($path);
        $this->assertSame(200, $width);
        $this->assertSame(200, $height);
    }

    public function test_arquivo_invalido_e_rejeitado(): void
    {
        Storage::fake('public');

        $porteiro = $this->porteiro();

        $this->actingAs($porteiro)
            ->post(route('perfil.foto.update'), [
                'foto' => UploadedFile::fake()->create('documento.pdf', 500, 'application/pdf'),
            ])
            ->assertSessionHasErrors('foto');

        $this->assertNull($porteiro->fresh()->foto_path);
    }

    public function test_arquivo_acima_de_2mb_e_rejeitado(): void
    {
        Storage::fake('public');

        $porteiro = $this->porteiro();

        $this->actingAs($porteiro)
            ->post(route('perfil.foto.update'), [
                'foto' => UploadedFile::fake()->image('grande.jpg')->size(3000),
            ])
            ->assertSessionHasErrors('foto');

        $this->assertNull($porteiro->fresh()->foto_path);
    }
}