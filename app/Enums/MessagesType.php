<?php
namespace App\Enums;
enum MessagesType: string
{
    case TEXT = 'text';

    case MEDIA = 'media';

    case AUDIO = 'audio';

    case LIST = 'list';
}
