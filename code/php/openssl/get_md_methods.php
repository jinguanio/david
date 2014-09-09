<?php
$digests             = openssl_get_md_methods();
$digests_and_aliases = openssl_get_md_methods(true);
$digest_aliases      = array_diff($digests_and_aliases, $digests);

print_r($digests);
print_r($digests_and_aliases);

print_r($digest_aliases);
