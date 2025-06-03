import pandas as pd
import mysql.connector

# Step 1: Read the CSV file
excel_file = './dummy_data_2.csv'  # Replace with your file path (CSV file)
df = pd.read_csv(excel_file)  # Use pd.read_csv for CSV instead of pd.read_excel

# Step 2: Connect to MySQL database
connection = mysql.connector.connect(
    host="localhost",
    user="root",  # MySQL username
    password="",  # MySQL password
    database="library"  # Replace with your database name
)

cursor = connection.cursor()

# Step 3: Iterate over DataFrame and insert each row into the 'books' table
for index, row in df.iterrows():
    # Extracting data from the row
    book_id = row['book_id']
    book_num = row['book_num']
    book_name = row['book_name']
    author_name = row['author_name']
    qty = row['available_quantity']
    book_edition = row['book_edition']
    publication = row['publication']
    faculty = row['faculty']
    sem = row['semester']
    total_qty = row['total_quantity']
    description = row['description']
    
    # Insert the data into the MySQL table
    insert_query = """
        INSERT INTO books (book_id, book_num, book_name, author_name, available_quantity, 
                           book_edition, publication, faculty, semester, total_quantity, description)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    
    # Execute the insert query
    cursor.execute(insert_query, (book_id, book_num, book_name, author_name, qty,
                                  book_edition, publication, faculty, sem, total_qty, description))

# Commit the transaction
connection.commit()

# Close the cursor and connection
cursor.close()
connection.close()

print("Data has been successfully inserted into the database.")
