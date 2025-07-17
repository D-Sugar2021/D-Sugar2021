import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.Statement;

public class CreateDB {
    public static void main(String[] args) {
        Connection c = null;
        Statement stmt = null;
        try {
            Class.forName("org.hsqldb.jdbc.JDBCDriver");
            c = DriverManager.getConnection("jdbc:hsqldb:file:exam", "SA", "");
            stmt = c.createStatement();

            String sql = "CREATE TABLE students (" +
                         "student_id INT PRIMARY KEY, " +
                         "first_name VARCHAR(50), " +
                         "last_name VARCHAR(50));" +

                         "CREATE TABLE questions (" +
                         "question_id INT PRIMARY KEY, " +
                         "question_text VARCHAR(255), " +
                         "question_type VARCHAR(20));" +

                         "CREATE TABLE answers (" +
                         "answer_id INT PRIMARY KEY, " +
                         "question_id INT, " +
                         "answer_text VARCHAR(255), " +
                         "is_correct BOOLEAN, " +
                         "FOREIGN KEY (question_id) REFERENCES questions(question_id));" +

                         "CREATE TABLE student_exams (" +
                         "student_exam_id INT PRIMARY KEY, " +
                         "student_id INT, " +
                         "exam_date DATE, " +
                         "score INT, " +
                         "FOREIGN KEY (student_id) REFERENCES students(student_id));" +

                         "CREATE TABLE student_answers (" +
                         "student_answer_id INT PRIMARY KEY, " +
                         "student_exam_id INT, " +
                         "question_id INT, " +
                         "answer_id INT, " +
                         "essay_answer LONGVARCHAR, " +
                         "FOREIGN KEY (student_exam_id) REFERENCES student_exams(student_exam_id), " +
                         "FOREIGN KEY (question_id) REFERENCES questions(question_id), " +
                         "FOREIGN KEY (answer_id) REFERENCES answers(answer_id));";

            stmt.executeUpdate(sql);
            stmt.close();
            c.close();
        } catch (Exception e) {
            System.err.println(e.getClass().getName() + ": " + e.getMessage());
            System.exit(0);
        }
        System.out.println("Database created successfully");
    }
}
