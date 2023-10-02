# Video API Documentation


[For Testing](tests/feature/personapitest.php)



## Introduction:
Welcome to the Video API documentation. This API allows you to upload, retrieve, and manage video files. Additionally, it integrates with WhisperAPI for video transcription.


## _Getting Started:_

##                     _REQUEST FORMAT AND RESPONSE FORMAT_
1.	_Clone the Repository:_ 


```php
git clone [repository-url]
cd project-folder
```


2.	_Install Dependencies:_

 
 ```php
composer install
```

3.	_Configure Environment:_ 
Copy the .env.example file to .env and configure your environment variables, including Cloudinary and WhisperAPI credentials.


4.	_Run Migrations:_ 

 
 ```php
 php artisan migrate
```

_Authentication:_
This API does not require authentication for now.
 
_Endpoints:_

** Video Upload (Store)

_Endpoint:_ POST /videos
_Description:_ Upload a video file.
_Request:_
Headers:
Content-Type: multipart/form-data
_Body:_
file (required): Video file (MP4 format)
thumbnail (optional): Thumbnail image file (JPG, PNG)
Response:
Success (201 Created):


  ```php
{
  "message": "Video uploaded and transcribed successfully",
  "video": {
    "id": 1,
    "title": "Example Video",
    "file_name": "cloudinary-public-id",
    "file_link": "cloudinary-secure-url",
    "thumbnail": "cloudinary-thumbnail-url",
    "file_size": "2.5 MB",
    "length": "00:05:30"
  },
  "transcription": "Transcribed text here"
}
```




*** Video Listing (Index)

_Endpoint:_ GET /videos
_Description:_ Retrieve a list of all videos.
_Response:_



  ```php
[
  {
    "id": 1,
    "title": "Example Video",
    "file_name": "cloudinary-public-id",
    "file_link": "cloudinary-secure-url",
    "thumbnail": "cloudinary-thumbnail-url",
    "file_size": "2.5 MB",
    "length": "00:05:30"
  },
  // Additional videos...
]
```



**** Video Retrieval (Show)

_Endpoint:_ GET /videos/{id}

_Description:_ Retrieve details for a specific video.
_Response:_



  ```php
{
  "id": 1,
  "title": "Example Video",
  "file_name": "cloudinary-public-id",
  "file_link": "cloudinary-secure-url",
  "thumbnail": "cloudinary-thumbnail-url",
  "file_size": "2.5 MB",
  "length": "00:05:30"
}
]
```




# _Error Handling:_

Common HTTP status codes and error messages.

## _Setting Up and Deploying the API Locally or on a Server:_
To set up and deploy the API, follow these general steps:
1.	Clone your Laravel project repository to your local machine using Git.
2.	Configure your database connection settings in the .env file.
3.	Install Composer dependencies: Run composer install in the project root directory.
4.	Generate an application key: Run php artisan key:generate.
5.	Run database migrations: Run php artisan migrate to create the necessary database tables.
6.	Start the development server: Run php artisan serve to run the API locally.
7.	Access the API endpoints using the base URL (http://localhost:8000/api) and follow the sample usage instructions mentioned earlier.
8.	Use Postman to test the API.
For deploying the API on a server, you will typically need to set up a web server (e.g., Apache or Nginx) and configure it to serve your Laravel application. The exact steps may vary depending on your server environment and hosting provider.
Remember to secure your server, configure environment variables, and follow best practices for production deployments.


## _Using Postman for API Testing_
Postman is a popular tool for testing APIs. You can use Postman to send HTTP requests to your API endpoints and validate the responses. Follow these steps to set up Postman for testing your Laravel API:
_Download and Install Postman:_
If you haven't already, download and install Postman from the official website.
Import API Requests:
You can import the API requests into Postman by using a collection. A collection is a group of saved requests. You can either manually create requests in Postman or import a JSON or YAML file containing the requests. To import your API requests, follow these steps:
In Postman, click on the "Import" button in the top-left corner.
Choose the option to import a "File" and select the JSON or YAML file that contains your API requests. Ensure that your requests are organized within folders and named appropriately for clarity.
Set Environment Variables (Optional):
If your API requires authentication tokens, base URLs, or other variables, you can define them as environment variables in Postman. This allows you to easily switch between different environments (e.g., development, production) without modifying the requests. To set up environment variables:
Click on the gear icon in the top-right corner of Postman.
Select "Manage Environments" and create a new environment. Define the necessary variables like base_url, api_key, or access_token.
Run API Requests:
Open the collection that contains your API requests.
Select the request you want to run.
Make sure you have set the appropriate environment if needed.
Click the "Send" button to send the request to your API.
Observe the response to verify that your API is working as expected.


## _Conclusion:_
Thank you for using the Video API. If you have any questions or issues, please contact [your contact information].
