import uno

def grade_exam(event):
    db_doc = event.Source.Model.Parent
    conn = db_doc.DataSource.getConnection("", "")
    stmt = conn.createStatement()

    # Get all student exams
    student_exams = stmt.executeQuery("SELECT student_exam_id FROM student_exams")
    while student_exams.next():
        student_exam_id = student_exams.getInt(1)

        # Get all student answers for this exam
        student_answers = stmt.executeQuery(f"SELECT question_id, answer_id, essay_answer FROM student_answers WHERE student_exam_id = {student_exam_id}")

        score = 0
        while student_answers.next():
            question_id = student_answers.getInt(1)
            answer_id = student_answers.getInt(2)
            essay_answer = student_answers.getString(3)

            # Check if the question is an MCQ
            question_type_rs = stmt.executeQuery(f"SELECT question_type FROM questions WHERE question_id = {question_id}")
            question_type_rs.next()
            question_type = question_type_rs.getString(1)

            if question_type == 'mcq':
                # Check if the answer is correct
                is_correct_rs = stmt.executeQuery(f"SELECT is_correct FROM answers WHERE answer_id = {answer_id}")
                is_correct_rs.next()
                is_correct = is_correct_rs.getBoolean(1)
                if is_correct:
                    score += 1
            else:
                # For essays, we can't auto-grade, so we'll just give them a point for now
                if essay_answer and len(essay_answer) > 0:
                    score += 1

        # Update the score
        stmt.executeUpdate(f"UPDATE student_exams SET score = {score} WHERE student_exam_id = {student_exam_id}")

    conn.close()
