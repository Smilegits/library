import serial
import pymysql
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import urllib.parse

# Set up serial connection (Ensure the COM port and baud rate match your configuration)
ser = serial.Serial('COM4', 9600)

# Connect to the database
connection = pymysql.connect(
    host='localhost',
    user='root',
    password='',
    database='library',
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)


# Function to fetch RFID IDs from tblstudents
def get_rfid_ids_from_students():
    with connection.cursor() as cursor:
        cursor.execute("SELECT Card_id FROM tblstudents")
        result = cursor.fetchall()
        return [str(row['Card_id']).upper() for row in result]


# Function to fetch RFID IDs from tblbooks
def get_rfid_ids_from_books():
    with connection.cursor() as cursor:
        cursor.execute("SELECT Book_CardID FROM tblbooks")
        result = cursor.fetchall()
        return [str(row['Book_CardID']).upper() for row in result]


# Function to read RFID data from Arduino
def read_rfid():
    rfid_data = ser.readline().decode().strip()
    print("RFID Data:", rfid_data)
    split_data = rfid_data.split(":")
    if len(split_data) < 2:
        split_data = rfid_data.split(": ")
    rfid_tag = split_data[-1].replace(" ", "").upper()
    print("RFID Tag:", rfid_tag)
    return rfid_tag


# Function to open a URL with parameters using Selenium
def open_url_with_selenium(url, params={}):
    driver = webdriver.Chrome()
    query_string = urllib.parse.urlencode(params)
    final_url = f"{url}?{query_string}"
    driver.get(final_url)
    return driver


# Main loop to simulate RFID data input and monitor URL changes
try:
    while True:
        # Read the RFID tag
        first_rfid_tag = read_rfid()

        # Check if the RFID tag is not empty
        if first_rfid_tag:
            print("RFID Data Read:", first_rfid_tag)

            # Check if the RFID tag is in the students database
            student_rfid_ids = get_rfid_ids_from_students()
            if first_rfid_tag in student_rfid_ids:
                print("RFID tag found in the students database.")

                # Open issue.php
                driver = open_url_with_selenium("http://localhost/HARDWARE/issue.php")

                # Wait for the page to load
                WebDriverWait(driver, 30).until(EC.url_contains("http://localhost/HARDWARE/issue.php"))

                print("Opened issue.php. Reading RFID card again.")

                # Read the second RFID tag (book RFID)
                second_rfid_tag = read_rfid()

                # Check if the second RFID tag is not empty
                if second_rfid_tag:
                    print("RFID Data Read:", second_rfid_tag)

                    # Check if the second RFID tag is in the books database
                    book_rfid_ids = get_rfid_ids_from_books()
                    if second_rfid_tag in book_rfid_ids:
                        print("RFID tag found in the books database.")

                        # Open issue_confirm.php
                        open_url_with_selenium("http://localhost/HARDWARE/issue_confirm.php", {"rfid": second_rfid_tag})
                    else:
                        print("RFID tag not found in the books database.")
                else:
                    print("No data read from the second RFID card.")
            else:
                print("RFID tag not found in the students database.")
        else:
            print("No data read from the first RFID card.")

except KeyboardInterrupt:
    print("Program interrupted")

except Exception as e:
    print("An error occurred:", e)

finally:
    # Close the Selenium WebDriver
    driver.quit()
