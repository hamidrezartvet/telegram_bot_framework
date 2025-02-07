PHP Telegram Bot  
An advanced and easy-to-use Telegram bot built in PHP, designed to handle automated interactions, commands, and user requests efficiently.  
  
Features  
✅ This bot is written in OOP structure.  
✅ Easy integration with Telegram API  
✅ Supports inline commands and buttons  
✅ MySQL database integration  
✅ Secure and optimized code structure  
✅ Date_helper class to convert date jalali to jeorjian  
✅ Sms service for sending sms by your bot  
✅ Image helper class for editing images  

Installation  

1. Clone the Repository  
git clone https://github.com/hamidrezartvet/telegram_bot_framework.git  
cd telegram_bot  
  
***change ownership of telegram-bot folder to www-data:www-date  
sudo chown -R www-data:www-data /var/www/telegram_bot  
  
***change permission telegram bot to 755  
sudo chmod -R 755 /var/www/telegram_bot  
  
*** Note: folder /telegram_bot/Storage/session and /telegram_bot/Storage/user_images  
need write and permission with user www-data  
  
2. Install Dependencies  
Ensure you have PHP 8+ and Composer installed, then run:  
composer install  
  
3. Api connection: this bot is designed to connect to Api. for example you have a main dashbord  
or an app and you need to connect your telegram bot to it.  
  
4. Configure Your Bot  
change configuration in Config->Config.php  
  
5. Run the Bot  
Start the bot using:  
php index.php  
  
Usage  
🔹 Send /start to begin interaction  
🔹 Use /help to see available commands  
🔹 Extend functionality with custom handlers  
  
Contributing  
Contributions are welcome! Feel free to submit a pull request or open an issue.  
  
About the Author  
👨‍💻 Hamid Reza Rasoli Tehrani– A skilled PHP developer, Linux expert, and AI enthusiast. Passionate about automation, cybersecurity, and blockchain.  
🔗 GitHub: https://github.com/hamidrezartvet  
🔗 Email:  hamidrezartvet@gmail.com

License  
📜 This project is open-source under the MIT License.  