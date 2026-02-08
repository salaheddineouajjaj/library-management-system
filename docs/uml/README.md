# UML Diagrams for Library Management System

This directory contains comprehensive UML diagrams for the Library Management System.

## 📊 Diagram Overview

### 1. **Global Use Case Diagram** (`01_global_usecase.puml`)
- **Description**: Shows all actors (Guest, User, Admin, AI System) and their interactions with the system
- **Actors**: Guest, User, Admin, AI System
- **Key Use Cases**: 
  - Guest: Register, Login, Browse Books
  - User: Borrow/Return books, Manage shelves, Get recommendations, Participate in forum
  - Admin: Manage users, books, categories, view reports

### 2. **Book Borrowing Use Case Diagram** (`02_borrowing_usecase.puml`)
- **Description**: Detailed view of book borrowing and management workflows
- **Focus Areas**:
  - Book search and browsing
  - Borrowing process with validation
  - Returning and extending borrowings
  - Admin book management
  - OpenLibrary API integration

### 3. **Global Sequence Diagram** (`03_global_sequence.puml`)
- **Description**: Complete flow from user login to borrowing a book
- **Shows**:
  - Authentication process
  - Dashboard loading
  - Book search
  - Borrowing transaction with all database interactions

### 4. **AI Recommendation Sequence Diagram** (`04_ai_recommendation_sequence.puml`)
- **Description**: AI-powered book recommendation flow
- **Shows**:
  - User history collection
  - Gemini API integration
  - Recommendation generation
  - Adding books to personal shelf

### 5. **Class Diagram** (`05_class_diagram.puml`)
- **Description**: Complete system architecture showing all entities
- **Classes**:
  - User, Admin, Book, Category
  - Borrowing, PersonalShelf, ShelfBook
  - ForumPost, ForumComment, Message
  - AIRecommendation
- **Relationships**: All associations, inheritances, and multiplicities

### 6. **Activity Diagram** (`06_activity_borrowing.puml`)
- **Description**: Book borrowing workflow with all validation steps
- **Shows**:
  - User flow from search to borrow
  - All validation checks (availability, eligibility, limits)
  - Database transaction
  - Error handling

## 🔧 How to Render These Diagrams

### Option 1: Online PlantUML Editor (Easiest)
1. Visit: https://www.plantuml.com/plantuml/uml/
2. Copy the content of any `.puml` file
3. Paste it into the editor
4. The diagram will render automatically
5. Download as PNG/SVG

### Option 2: Visual Studio Code Extension
1. Install "PlantUML" extension by jebbs
2. Open any `.puml` file
3. Press `Alt + D` to preview
4. Right-click → Export to PNG/SVG

### Option 3: Command Line (Java required)
```bash
# Install PlantUML
curl -L https://sourceforge.net/projects/plantuml/files/plantuml.jar/download -o plantuml.jar

# Render a diagram
java -jar plantuml.jar 01_global_usecase.puml

# Render all diagrams
java -jar plantuml.jar docs/uml/*.puml
```

### Option 4: IntelliJ IDEA / PyCharm
1. Install "PlantUML integration" plugin
2. Right-click on `.puml` file
3. Select "Show PlantUML Diagram"

## 📁 Output Formats

PlantUML can export to:
- **PNG** (default, good for documentation)
- **SVG** (scalable, best for presentations)
- **PDF** (print-ready)
- **LaTeX** (for academic papers)

## 🎨 Diagram Features

All diagrams include:
- ✅ Clean, professional styling
- ✅ Clear actor/component names
- ✅ Relationship types (include, extend, association)
- ✅ Notes and annotations
- ✅ Proper UML notation

## 📖 Documentation Standards

These diagrams follow:
- **UML 2.5** standard notation
- **Consistent naming** conventions
- **Clear relationships** with multiplicities
- **Comprehensive coverage** of all features

## 📝 Updating Diagrams

To modify diagrams:
1. Edit the `.puml` file
2. Re-render using any method above
3. Update documentation if needed

## 💡 Tips

- Use **Alt + D** in VS Code for live preview
- Export as **SVG** for best quality in presentations
- Use **PNG** for embedding in Word/PDF documents
- **plantuml.com** is great for quick previews

## 🔗 Resources

- [PlantUML Official Site](https://plantuml.com/)
- [PlantUML Language Reference](https://plantuml.com/guide)
- [PlantUML Online Editor](https://www.plantuml.com/plantuml/uml/)

---

**Created for**: Library Management System  
**Version**: 1.0  
**Date**: 2026-02-08
