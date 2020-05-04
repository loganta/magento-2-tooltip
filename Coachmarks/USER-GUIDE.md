## Documentation

## How to install

### Install ready-to-paste package (Recommended)

- Installation guide: https://www.SM.com/install-magento-2-extension/

## How to upgrade

1. Backup

Backup your Magento code, database before upgrading.

2. Remove Coachmarks folder

In case of customization, you should backup the customized files and modify in newer version.
Now you remove `app/code/SM/Coachmarks` folder. In this step, you can copy override Coachmarks folder but this may cause of compilation issue. That why you should remove it.

3. Upload new version
Upload this package to Magento root directory

4. Run command line:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
