# انتخاب تصویر پایه اصلی PHP همراه Apache
FROM php:8.2-apache

# فعال‌سازی mod_rewrite آپاچی
RUN a2enmod rewrite

# نصب افزونه‌های پرکاربرد PHP (به دلخواه می‌توانید اضافه/کم کنید)
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-enable mysqli pdo pdo_mysql

# انجام تنظیمات پیشنهادی (php.ini development)
# می‌تونید تنظیمات را به دلخواه تغییر دهید یا فایل اختصاصی اضافه کنید
# COPY custom-php.ini /usr/local/etc/php/conf.d/

# کپی کردن سورس پروژه به روت داکر (پوشه public_html شما)
COPY . /var/www/html/

# اگر فایل env دارید، اینجا کپی کنید
# COPY .env /var/www/html/.env

# تنظیمات Permisson (اختیاری)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# تنظیم timezone (اختیاری)
RUN echo "date.timezone = Asia/Tehran" > /usr/local/etc/php/conf.d/timezone.ini

# اکسپوز کردن پورت 80
EXPOSE 80

# اجرای پیشنهاد شده برای Apache (پیش‌فرض)
CMD ["apache2-foreground"]
