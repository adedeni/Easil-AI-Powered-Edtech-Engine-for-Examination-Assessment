import { Routes, Route } from "react-router-dom";
import AdminLayout from "../layouts/AdminLayout";
import Dashboard from "../dashboards/admin/Dashboard";
import Results from "../dashboards/admin/Results";
import Teachers from "../dashboards/admin/Teachers";
import Lecturers from "../dashboards/admin/Lecturers";
import Students from "../dashboards/admin/Students";
import Courses from "../dashboards/admin/Courses";
import Reports from "../dashboards/admin/Reports";

export default function AdminRoutes() {
  return (
    <Routes>
      <Route path="/" element={<AdminLayout />}>
        <Route index element={<Dashboard />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="results" element={<Results />} />
        <Route path="teachers" element={<Teachers />} />
        <Route path="lecturers" element={<Lecturers />} />
        <Route path="students" element={<Students />} />
        <Route path="courses" element={<Courses />} />
        <Route path="reports" element={<Reports />} />
      </Route>
    </Routes>
  );
}
