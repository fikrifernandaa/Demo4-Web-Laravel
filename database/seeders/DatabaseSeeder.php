<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Data awal sama dengan MediaLibrary di Modul 3 (+ 2 tambahan agar pencarian bervariasi).
        $animeList = [
            ['Attack on Titan', 'Action, Drama', 9.1, 25, 'High Energy',
                'Manusia berjuang bertahan hidup dari serangan para Titan.'],
            ['Your Name', 'Romance, Fantasy', 8.8, 1, 'Sweet Romance',
                'Dua remaja mengalami kejadian misterius yang menghubungkan mereka.'],
            ['Death Note', 'Mystery, Psychological', 9.0, 37, 'Mind Bending',
                'Seorang siswa menemukan buku yang dapat menyebabkan kematian.'],
            ['Fullmetal Alchemist: Brotherhood', 'Action, Adventure', 9.1, 64, 'Epic Adventure',
                'Dua bersaudara mencari Batu Bertuah untuk memulihkan tubuh mereka.'],
            ['Demon Slayer', 'Action, Fantasy', 8.7, 26, 'High Energy',
                'Seorang pemuda menjadi pemburu iblis demi menyelamatkan adiknya.'],
        ];

        foreach ($animeList as [$title, $genre, $score, $episodes, $aura, $synopsis]) {
            Media::firstOrCreate(
                ['title' => $title],
                [
                    'type'     => 'Anime',
                    'genre'    => $genre,
                    'score'    => $score,
                    'episodes' => $episodes,
                    'aura'     => $aura,
                    'synopsis' => $synopsis,
                ]
            );
        }

        $fikri = User::firstOrCreate(
            ['email' => 'fikri@example.com'],
            ['name' => 'Fikri', 'password' => Hash::make('password')]
        );

        User::firstOrCreate(
            ['email' => 'aqila@example.com'],
            ['name' => 'Aqila', 'password' => Hash::make('password')]
        );

        // Koleksi awal Fikri (dua media pertama).
        $fikri->media()->syncWithoutDetaching(
            Media::orderBy('id')->limit(2)->pluck('id')
        );
    }
}
