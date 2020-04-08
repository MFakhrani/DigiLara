<p align="center"><img src="https://digilara.ir/assets/img/logo.png" width="200px"></p>



## About Digilara Updater

Following changes to be made

- Keep the project in project-name folder (Example digilara files in **digialra** folder)

#### Outcome
- New version will be created in **versions** folder
- auto update will be created in **versions/auto-update/product-name** folder

## Installation

        composer require fakhrani/digilara
    
## Publish files
        php artisan vendor:publish --provider="Fakhrani\Digilara\FakhraniDigilaraServiceProvider"
    
## Command to run for creating new version
        php artisan script:version {version}

