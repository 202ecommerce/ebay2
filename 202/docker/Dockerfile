FROM 202ecommerce/prestashop:1.7.8.7

RUN rm -Rf var/www/html/modules/ebay/

WORKDIR /var/www/html/modules/ebay/

ENTRYPOINT ["sh", "202/docker/entrypoint.sh"]
