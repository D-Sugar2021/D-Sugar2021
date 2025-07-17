import uno
import random

def show_random_questions(event):
    doc = event.Source.Model.Parent
    conn = doc.DataSource.getConnection("","")
    stmt = conn.createStatement()

    # Get all question IDs
    rs = stmt.executeQuery("SELECT question_id FROM questions")
    all_questions = []
    while rs.next():
        all_questions.append(rs.getInt(1))

    # Select 10 random questions
    random_questions = random.sample(all_questions, 10)

    # This is a simplified example. A real implementation would involve
    # dynamically creating the form controls for the selected questions.
    # For now, we'll just print the selected questions.
    print(random_questions)

    conn.close()
