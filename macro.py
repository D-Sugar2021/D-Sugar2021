import uno

def create_schema(event):
    db_doc = event.Source.Model.Parent
    conn = db_doc.DataSource.getConnection("", "")
    stmt = conn.createStatement()

    sql = """
    -- Students table
    CREATE TABLE students (
        student_id INT PRIMARY KEY,
        first_name VARCHAR(50),
        last_name VARCHAR(50)
    );

    -- Questions table
    CREATE TABLE questions (
        question_id INT PRIMARY KEY,
        question_text VARCHAR(255),
        question_type VARCHAR(20) -- 'mcq' or 'essay'
    );

    -- Answers table for MCQs
    CREATE TABLE answers (
        answer_id INT PRIMARY KEY,
        question_id INT,
        answer_text VARCHAR(255),
        is_correct BOOLEAN,
        FOREIGN KEY (question_id) REFERENCES questions(question_id)
    );

    -- Student exams table
    CREATE TABLE student_exams (
        student_exam_id INT PRIMARY KEY,
        student_id INT,
        exam_date DATE,
        score INT,
        FOREIGN KEY (student_id) REFERENCES students(student_id)
    );

    -- Student answers table
    CREATE TABLE student_answers (
        student_answer_id INT PRIMARY KEY,
        student_exam_id INT,
        question_id INT,
        answer_id INT, -- For MCQs
        essay_answer TEXT, -- For essays
        FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id),
        FOREIGN KEY (question_id) REFERENCES questions(question_id),
        FOREIGN KEY (answer_id) REFERENCES answers(answer_id)
    );
    """

    stmt.execute(sql)
    conn.close()
