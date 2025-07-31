import { Routes, Route } from "react-router-dom";
import LecturerLayout from "../layouts/LecturerLayout";
import Dashboard from "../dashboards/lecturer/Dashboard";
import Students from "../dashboards/lecturer/Students";
import Courses from "../dashboards/lecturer/Courses";
import CreateExam from "../dashboards/lecturer/CreateExam";
import ExamManagement from "../dashboards/lecturer/ExamManagement";
import Reports from "../dashboards/lecturer/Reports";

export default function LecturerRoutes() {
  return (
    <Routes>
      <Route path="/" element={<LecturerLayout />}>
        <Route index element={<Dashboard />} />
        <Route path="students" element={<Students />} />
        <Route path="courses" element={<Courses />} />
        <Route path="exams" element={<ExamManagement />} />
        <Route path="exam-management" element={<ExamManagement />} />
        <Route path="create-exam" element={<CreateExam />} />
        <Route path="reports" element={<Reports />} />
      </Route>
    </Routes>
  );
}
