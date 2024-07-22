<?php

namespace App\Enums;

enum MessagesType: string
{
    case TEXT = 'text';

    case IMAGE = 'image';

    case STICKER = 'sticker';

    case VIDEO = 'video';

    case MEDIA_AUDIO = 'media_audio';

    case AUDIO = 'audio';

    case LIST = 'list';

    case POOL = 'pool';

    case STATUS = 'status';
}
