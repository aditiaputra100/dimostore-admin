<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Can upload file test', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product1.jpg');

    $path = $file->store('products', 'public');

    expect($path)->not()->toBeEmpty();

    $isExist = Storage::disk('public')->exists($path);

    expect($isExist)->toBeTrue();
    
});
