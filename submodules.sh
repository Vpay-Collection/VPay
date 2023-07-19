#!/bin/bash

#
# Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
#

submodules=(
    "https://github.com/VPay-Collection/vpay-sdk src/library/vpay"
    "https://github.com/Clean-PHP/CleanPHP/ src/cleanphp"
    "https://github.com/Clean-PHP/captcha/ src/library/captcha"
    "https://github.com/Clean-PHP/database/ src/library/database"
    "https://github.com/Clean-PHP/encryption/ src/library/encryption"
    "https://github.com/Clean-PHP/http/ src/library/http"
    "https://github.com/Clean-PHP/login/ src/library/login"
    "https://github.com/Clean-PHP/mail/ src/library/mail"
    "https://github.com/Clean-PHP/qrcode/ src/library/qrcode"
    "https://github.com/Clean-PHP/task/ src/library/task"
    "https://github.com/Clean-PHP/upload/ src/library/upload"
    "https://github.com/Clean-PHP/verity/ src/library/verity"
)

for submodule in "${submodules[@]}"; do
    url="${submodule%% *}"
    path="${submodule#* }"
    git submodule add "$url" "$path"
done
