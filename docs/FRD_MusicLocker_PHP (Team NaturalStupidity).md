# Functional Requirements Document (FRD) 

# **1\. Project Title**

Music Locker: Your Personalized Music and Albums Repository

# **2\. Project Overview**

Music Locker is a PHP-based web application that allows users to create and manage their personal music catalog without relying on external streaming platforms. The application solves the problem of scattered music discoveries and forgotten favorite songs by providing a private, organized space where music enthusiasts can log their favorite tracks, albums, and associated memories. It will be used by individuals who want to maintain a personal record of their music taste, track songs they discover, and organize their musical preferences in a simple, distraction-free environment without algorithm interference or subscription requirements.

# **3\. Scope**

The scope of Music Locker details what will be included and what will not.

* **In Scope:**  
  * User account creation and authentication  
  * Personal music catalog management (add, edit, delete songs/albums)  
  * Personal notes and mood tagging system  
  * Mobile-responsive web interface  
  * ACID-compliant database transactions  
  * Basic offline functionality through caching  
  * Search and filter functionality within personal collection  
  * Session management and security features  
  * Integration with external music APIs for song metadata lookup

* **Out Scope:**  
  * Music file uploading or hosting  
  * Music streaming or playback functionality  
  * Social features or sharing between users  
  * Music recommendation algorithms  
  * Advanced analytics or reporting features  
  * Mobile app exclusive interface  
  * Multi-language support

# **4\. Functional Requirements**

The function requirements details on what Music Locker should be able to perform after development.

* User registration and login system  
* Secure session management and logout functionality  
* Add new songs and albums to personal collection  
* Edit and update existing music entries  
* Delete songs and albums from collection  
* Add personal notes and memories to music entries  
* Create and assign custom mood/vibe tags  
* Search through personal music collection  
* Filter collection by artist, album, genre, or mood tags  
* View detailed information for each music entry  
* Basic collection statistics and overview dashboard  
* Data export functionality for personal backup

# **5\. User Roles & Permissions**

This section identifies which users the project wishes to cater to and their permissions when using the service.

* **Registered User**  
  * Create and manage personal music collection  
  * Search external APIs for songs  
  * Add/edit/delete personal entries  
  * Create custom tags and notes  
  * Change account settings

* **Admin**   
  * Access admin dashboard  
  * Manage user accounts  
  * Reset user passwords  
  * Monitor system usage  
  * Handle database maintenance  
  * Configure external API settings

# 

# **6\. System Flow**

 This section details the flowchart for Music Locker internal process

# **7\. Technology Stack**

This section will detail the specification in which Music Locker will partake during development:

* **Backend:** PHP 8.2+ (core language)  
* **Frontend:** HTML5, CSS3, JavaScript (ES6+)  
* **Database:** MySQL 8.0 (for ACID compliance)  
* **Frameworks**: Vanilla PHP (optional)  
* **CSS Framework**: Bootstrap (for responsive design)  
* **External APIs**: Spotify Web (for song query)  
* **Caching**: Browser localStorage for offline functionality  
* **Security**: PHP sessions, password hashing (bcrypt), CSRF protection  
* **Development Tools**: Composer for dependency management

# **8\. Constraints & Assumptions**

This section will specify the limitations that Music Locker may encounter during development and deployment:

* Internet connection required for external API music searches and initial login  
* Users must have a valid email address for registration and password recovery  
* Modern web browser required (Chrome 90+, Firefox 88+, Safari 14+) for full functionality  
* JavaScript must be enabled for dynamic features and offline functionality  
* External API rate limits may restrict the number of music searches per hour  
* No mobile app version \- web-only responsive design  
* Single-user sessions \- no concurrent login support from multiple devices  
* English language only for the initial version  
* Basic hosting requirements \- PHP-enabled web server with database support

---

Prepared By:

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_		              \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_  
Reynaldo D. Grande Jr. II			 Louis Jansen G. Letgio

	\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_			\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_  
	Euzyk Kendyl Villarino				Shawn Patrick R. Dayanan

Date: **August 17, 2025**