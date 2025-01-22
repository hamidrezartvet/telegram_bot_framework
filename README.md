# telegram bot framework in php
telegram bot framework in php _ easy to use  
this framework is written in pure php. I tried to make easy and enjoyable to use each part of the framework  
your comments and contributions can improve this project and make work easier for other too.  
this project is writtem by love...  

what does this sample app do?  
-user authentication with username and password based on respond from server
-display menu
-several part task in telegram bot  

note: this bot uses FSM system for maintain functions. this method make everything easy and for new functions only you need to add new functions with tasks in the FSM array.  
  
 
/**  
 * Guideline to run this project on server step by step:  
 */  
1-download and put this project in webserver  
2-point telegram webhook address to index.php  
command to point from broweser:  
https://api.telegram.org/<REPLACE_BOT_TOKEN>/setWebhook?url=https://yourdomain.com/your-webhook-path  
  
*remember: if you want to sethook from Iran , start vpn then set webhook  
  
3-define telegram bot and varibles in constants.php  
4-in constant there is a variable by the name of "API_TOKEN". this token is suggested to use for safe communication between your bot server and another server. for example if you have one main server and a telegram bot server , for safe communication it is suggested to use token.
5-bot is ready!