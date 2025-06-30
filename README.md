# 🏥 Hospital Stock Management

Comprehensive management system for controlling the stock of healthcare products in hospitals, floors and first aid kits.

## 📝 Introduction

Hospital Stock Management is a web system designed to optimize the management of healthcare products in hospital environments. It allows inventory control and product movements between different locations (hospitals, floors, warehouses, and first aid kits), facilitating real-time tracking of available stock and improving efficiency in medical resource management.

## ✨ Main Features

- **Hierarchical Management**: Administration of hospitals, floors, warehouses, and first aid kits
- **Inventory Control**: Precise tracking of available stock at each location
- **Movement Management**: Transfers, entries, and returns of products
- **Complete Traceability**: Detailed history of all movements made
- **User and Role System**: Permission-based access control
- **Responsive Interface**: Designed to work on mobile devices and desktop

## 🛠️ Technologies Used

- **Backend**: PHP 8.2 (OOP, MVC)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Database**: MySQL 8.0
- **Environment**: Docker, Docker Compose
- **Other tools**: Composer, PHPMyAdmin

## 🚀 Installation and Deployment

### Prerequisites

- Docker and Docker Compose installed
- Git

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Piioni/Gestion_hospitalaria.git
   cd Gestion_hospitalaria
   ```

2. **Start the containers with Docker Compose**:
   ```bash
   docker-compose up -d
   ```

3. **Import the database**:
    - Access PHPMyAdmin at `http://localhost:8080`
    - Username: `root`, Password: `rootpassword`
    - Create a new database called `stock_hospitalario`
    - Import the file `database/stock_hospitalario.sql`

4. **Access the application**:
    - Open your browser and go to `http://localhost:8000`
    - Access credentials:
        - **Administrator**: admin@example.com / admin123
        - **Manager**: gestor@example.com / gestor123

## 🏗️ Hierarchical Structure

The system follows a hierarchical structure for entity organization:

```
Hospital
└── Floor
    ├── Warehouse
    └── First Aid Kit
        └── Products
```

## 📊 Workflow

### Hospital Management

- **Creation**: Register new hospitals in the system
- **Editing**: Modify existing hospital information
- **Visualization**: View details and associated floors
- **Deletion**: Delete hospitals that don't have associated floors

### Floor Management

- **Creation**: Associate floors to a specific hospital
- **Editing**: Update existing floor information
- **Visualization**: View details, warehouses, and first aid kits
- **Deletion**: Delete floors that don't have associated warehouses or first aid kits

### Warehouse Management

- **Creation**: Configure warehouses on specific floors
- **Editing**: Modify existing warehouse parameters
- **Stock**: Manage the inventory of available products
- **Deletion**: Delete warehouses without active stock

### First Aid Kit Management

- **Creation**: Register first aid kits on specific floors
- **Assignment**: Associate specific products and quantities
- **Monitoring**: Track product consumption and status
- **Deletion**: Delete first aid kits without associated products

### Stock Movements

- **Transfers**: Move products between different warehouses
- **Entries**: Register the incorporation of new products
- **Returns**: Manage the return of products from first aid kits to warehouses

### Important Considerations

- **Logical Deletion**: Entities are marked as inactive but not deleted from the database
- **Deletion Restrictions**: Entities with associated child elements cannot be deleted:
    - Hospitals cannot be deleted if they have floors
    - Floors cannot be deleted if they have warehouses or first aid kits
    - Warehouses cannot be deleted if they have stock
    - First aid kits cannot be deleted if they have assigned products

## 📸 Screenshots

### Main Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Hospital Management
![Hospitals](docs/screenshots/hospitales.png)

### Stock Management
![Stock](docs/screenshots/stock.png)

## 📂 Project Structure

```
stock_hospitalario/
├── app/                     # Application code
│   ├── src/                 # Source code
│   │   ├── controllers/     # MVC controllers
│   │   ├── model/           # Models and entities
│   │   │   ├── entity/      # Entity classes
│   │   │   ├── repository/  # Repositories for data access
│   │   │   └── service/     # Business logic services
│   │   └── view/            # Views and templates
│   └── public/              # Public files
│       ├── assets/          # CSS, JS, images
│       └── index.php        # Entry point
├── database/                # SQL scripts
├── docker/                  # Docker configuration
└── docker-compose.yml       # Service configuration
```

---

Developed with ❤️ by Juan Rangel!
