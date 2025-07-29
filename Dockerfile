# نسخه پایه PHP با Apache
FROM php:8.2-apache

# فعال‌سازی ماژول rewrite
RUN a2enmod rewrite

# پورت 80 برای وب سرور باز باشد
EXPOSE 80
