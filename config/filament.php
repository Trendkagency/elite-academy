<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to store files. We default
    | this to the 'public' disk so all admin uploads (avatars, covers,
    | media) are accessible publicly and never trapped in private storage.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', env('FILESYSTEM_DISK', 'public')),

];
