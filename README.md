# SCSE Curriculum DMS

## Getting Started

### Prerequisites (Windows OS)

#### Install Git

1. Download and install git from: https://git-scm.com/download/win

#### Download Project Source code

1. Create a folder name 'SCSE_Curriculum_DMS' in your desired dictory

2. Run Command Prompt and change dictory to the 'SCSE_Curriculum_DMS' folder

3. Download the project's source code from: https://github.com/evancjx/SCSE_Curriculum_DMS by running the command below:

``` git pull https://github.com/evancjx/SCSE_Curriculum_DMS.git master ```

#### To Install Localhost server (Apache), MySQL and PHP

1. Download XAMPP from https://www.apachefriends.org/xampp-files/7.3.12/xampp-windows-x64-7.3.12-0-VC15-installer.exe

2. Install to your desired dictory on your machine<br>
Select only <b>Apache</b> and <b>MySQL</b>

3. Once installed, run XAMPP, click on Config on Apache row. Select Apache (httpd.conf)

4. Hold Ctrl + F to search for 'DocumentRoot'

5. Modify DocumentRoot to "[project_location]\SCSE_Curriculum_DMS\CorePHP"<br>
Do the same for Directory "[project_location]\SCSE_Curriculum_DMS\CorePHP"<br>
Similar to:

```
DocumentRoot "D:\Dropbox\SCSE_Curriculum_DMS\CorePHP"
<Directory "D:\Dropbox\SCSE_Curriculum_DMS\CorePHP">
```

#### To Install Laravel

1. Ensure PHP is installed

### To Run CorePHP Website

1. Ensure XAMPP is running in background

2. Apache and MySQL modules are installed and <u>started</u> in XAMPP

3. The website can be located at localhost or 127.0.0.1 in your desired browser

### To Run Laravel-based website

1. Double-click on 'RunLaravel.bat' from <b>Laravel</b> folder