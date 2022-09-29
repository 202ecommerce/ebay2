# Ebay

## About

Let the merchant use eBay as a new selling place.

Launched in 2012, the official eBay module became paid in 2017 after eBay stopped sponsoring the module.

In order to allow each PrestaShop merchant to start or grow their business on eBay without constraint, 202 ecommerce released the official eBay module in open source license as of July 1, 2022. The module code will be free and public on this Github repository to allow merchants to continue their activity with eBay2 module. 

Developers will be able to make a "fork" to make available their patches or evolutions that they have developed. A zip containing corrections made by 202 ecommerce will be published by September 1st 2022 on GitHub and addons.prestashop.com. After this date, updates will only be available on GitHub.

The support of the module through addons.prestashop.com will be provided until the end of the Business Care subscriptions. Once expired, these subscriptions will not be renewable. If you need support after your Business Care expires, please contact us for a list of freelance developers or a quote.

Do you have a question? Contact us at ebay2official@202-ecommerce.com

Free module documentation, available at this address:
https://desk.202-ecommerce.com/portal/en/kb/addons-prestashop-help-center/ebay-2-official


## Contributing

PrestaShop modules are open-source extensions to the PrestaShop e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.

### Requirements

Contributors **must** follow the following rules:

* **Make your Pull Request on the "dev" branch**, NOT the "master" branch.
* Do not update the module's version number.
* Follow [the coding standards][1].

### Process in details

Contributors wishing to edit a module's files should follow the following process:

1. Create your GitHub account, if you do not have one already.
2. Fork the ebay project to your GitHub account.
3. Clone your fork to your local machine in the ```/modules``` directory of your PrestaShop installation.
4. Create a branch in your local clone of the module for your changes.
5. Change the files in your branch. Be sure to follow [the coding standards][1]!
6. Push your changed branch to your fork in your GitHub account.
7. Create a pull request for your changes **on the _'dev'_ branch** of the module's project. Be sure to follow [the commit message norm][2] in your pull request. If you need help to make a pull request, read the [Github help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open-source project! Congratulations!

### Command line launched by github actions

Please launch these command line before submitting a Pull Request.

#### phpstan

You need a docker container to launch phpstan:

```
# create the prestashop container
~$ docker run -tid --rm -v ps-volume:/var/www/html --name temp-ps-ebay prestashop/prestashop

# launch phpstan
~$ docker run --rm --volumes-from temp-ps-ebay -v $PWD:/var/www/html/modules/ebay -e _PS_ROOT_DIR_=/var/www/html --workdir=/var/www/html/modules/ebay phpstan/phpstan:0.12 analyse --configuration=/var/www/html/modules/ebay/202/phpstan/phpstan.neon
```


[1]: http://doc.prestashop.com/display/PS16/Coding+Standards
[2]: http://doc.prestashop.com/display/PS16/How+to+write+a+commit+message
[3]: https://help.github.com/articles/using-pull-requests
