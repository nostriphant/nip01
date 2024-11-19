<?php

namespace nostriphant\NIP01\Key;

/**
 * Description of Format
 *
 * @author Rik Meijer <hello@rikmeijer.nl>
 */
enum Format {
    case BINARY;
    case HEXIDECIMAL;
    case BECH32;
}