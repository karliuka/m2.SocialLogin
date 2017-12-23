# Magento2 Social Login

Extension allows your user to login or register by using their existing account from social networks.

### Login page

<img alt="Magento2 Social Login" src="https://karliuka.github.io/m2/social-login/login.png" style="width:100%"/>

### Profile page

<img alt="Magento2 Social Login" src="https://karliuka.github.io/m2/social-login/profile.png" style="width:100%"/>

### Configuration page

<img alt="Magento2 Social Login" src="https://karliuka.github.io/m2/social-login/config.png" style="width:100%"/>

## Install with Composer as you go

1. Go to Magento2 root folder

2. Enter following commands to install module:

    ```bash
    composer require faonni/module-social-login
    ```
   Wait while dependencies are updated.

3. Enter following commands to enable module:

    ```bash
	php bin/magento setup:upgrade
	php bin/magento setup:di:compile
	php bin/magento setup:static-content:deploy  (optional)

