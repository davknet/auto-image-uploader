# auto-image-uploader

Auto Image Uploader is a WordPress plugin that syncs images between client-side and backend servers via REST API or SOAP. It temporarily stores large files on the client-side server, reducing upload delays and improving user experience by syncing files to the backend server later.

Auto Image Uploader is a WordPress plugin designed to streamline image synchronization between servers. In scenarios where two servers are involved—such as a client-side server and a backend server—communication is established via REST API or SOAP.

Large file uploads from the client-side server can often result in delays, causing inconvenience for users and a suboptimal experience. This plugin addresses that issue by allowing files to be temporarily stored on the client-side server. The plugin then handles the synchronization of these files with the backend server at a later time, ensuring a smoother and more efficient process for both users and administrators.





I automated the process using two approaches:

WordPress Cron: This method leverages WordPress's built-in scheduling system. However, WordPress cron is not fully reliable since it only triggers when a user visits the website, making it unsuitable for time-critical tasks.

Server Cron Job: To ensure consistent and timely execution, I implemented a server-level cron job. This approach is independent of website traffic and provides greater reliability for scheduled tasks.

Additionally, I created a reusable route  that simplifies the setup of server cron jobs, making it easy for others to configure and integrate into their workflows
