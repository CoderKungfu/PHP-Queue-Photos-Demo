<?php
# Rename this file to env.php and place in the same folder as cli.php and daemon.php

putenv("backend_target=WindowsAzureServiceBus");
putenv("upload_target=WindowsAzureBlobUploadContainer");
putenv("cdn_target=WindowsAzureBlobCDNContainer");

putenv("queue_connection_string='Endpoint=https://yournamespace.servicebus.windows.net/;SharedSecretIssuer=owner;SharedSecretValue=XXXXX'");
putenv("wa_blob_connection_string='DefaultEndpointsProtocol=https;AccountName=youraccount;AccountKey=XXXXXX'");