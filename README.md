# CERICar - Modern Carpooling Platform

CERICar is a full-stack web application designed to facilitate carpooling. It allows users to search for trips, manage bookings, and offer rides. The platform focuses on a seamless user experience with a responsive design and asynchronous interactions.

## 🚀 Features

* **Advanced Search Engine**: Find direct trips or complex journeys with automatic connections based on timing constraints.
* **Driver & Passenger Management**: Dedicated profiles for users to manage their roles, driver licenses, and vehicle details.
* **Real-time Availability**: Dynamic calculation of remaining seats based on current reservations.
* **Interactive UI**: AJAX-powered search and notifications for a smooth, no-reload experience.
* **Responsive Design**: Fully optimized for mobile, tablet, and desktop viewing.

## 🛠 Tech Stack

* **Backend**: PHP 8.x (Yii Framework)
* **Database**: PostgreSQL
* **Frontend**: HTML5, CSS3, JavaScript (jQuery / AJAX)
* **Pattern**: Model-View-Controller (MVC)
* **ORM**: Yii ActiveRecord

## 📋 Database Schema

The application manages several core entities:
- **Internaute**: User management and authentication.
- **Trajet**: Pre-defined routes between cities.
- **Voyage**: Specific trip instances offered by drivers.
- **Reservation**: Booking management and seat tracking.
- **Vehicle Specs**: Integration of brands and vehicle types.

## ⚙️ Setup & Installation

1. **Clone the repository**
   ```bash
   git clone [https://github.com/yourusername/cericar-web-app.git](https://github.com/yourusername/cericar-web-app.git)
   cd cericar-web-app
