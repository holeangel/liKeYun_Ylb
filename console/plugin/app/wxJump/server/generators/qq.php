<?php
function generateQQScheme($type, $id) {
    if ($type === 'group') {
        return "qq://group/join?key=$id";
    } else {
        return "qq://im/msg?uin=$id";
    }
}