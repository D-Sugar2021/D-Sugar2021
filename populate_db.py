import csv
import random

# Generate student data
with open('students.csv', 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['student_id', 'first_name', 'last_name'])
    for i in range(1, 101):
        writer.writerow([i, f'Student_{i}', f'Name_{i}'])

# Generate question data
with open('questions.csv', 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['question_id', 'question_text', 'question_type'])
    for i in range(1, 11):
        writer.writerow([i, f'MCQ Question {i}', 'mcq'])
    for i in range(11, 16):
        writer.writerow([i, f'Essay Question {i}', 'essay'])

# Generate answer data
with open('answers.csv', 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['answer_id', 'question_id', 'answer_text', 'is_correct'])
    answer_id = 1
    for i in range(1, 11):
        for j in range(1, 5):
            is_correct = (j == 1)
            writer.writerow([answer_id, i, f'Answer {j} for question {i}', is_correct])
            answer_id += 1
