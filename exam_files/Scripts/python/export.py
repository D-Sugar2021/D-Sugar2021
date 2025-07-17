import uno
import csv

def export_results(event):
    db_doc = event.Source.Model.Parent
    conn = db_doc.DataSource.getConnection("", "")
    stmt = conn.createStatement()

    # Get all student exam results
    rs = stmt.executeQuery("SELECT s.first_name, s.last_name, se.exam_date, se.score FROM students s JOIN student_exams se ON s.student_id = se.student_id")

    with open('results.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['First Name', 'Last Name', 'Exam Date', 'Score'])
        while rs.next():
            writer.writerow([rs.getString(1), rs.getString(2), rs.getDate(3), rs.getInt(4)])

    conn.close()

    # This is a simplified example. A real implementation would convert the CSV to Excel format.
    # For now, we will just create the CSV file.
    print("Results exported to results.csv")
