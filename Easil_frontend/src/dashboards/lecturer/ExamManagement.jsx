import React, { useState } from "react";
import { 
  Search, 
  Filter, 
  Plus,
  Eye,
  Edit,
  Trash2,
  Calendar,
  CheckCircle,
  Clock,
  AlertCircle
} from "lucide-react";
import { useNavigate } from "react-router-dom";

const ExamManagement = () => {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCourse, setSelectedCourse] = useState("");
  const [selectedStatus, setSelectedStatus] = useState("");
  const [selectedDate, setSelectedDate] = useState("10/11/2024");

  // Sample exam data matching the design
  const [exams] = useState([
    {
      id: 1,
      courseTitle: "Fundamentals of E-Commerce",
      courseCode: "CSC 616",
      date: "09, Sept, 2024",
      status: "ongoing",
      statusColor: "orange"
    },
    {
      id: 2,
      courseTitle: "Course Title Number 1",
      courseCode: "CSC 601",
      date: "18, Sept, 2024",
      status: "upcoming",
      statusColor: "red"
    },
    {
      id: 3,
      courseTitle: "Course Title Number 2",
      courseCode: "CSC 602",
      date: "6, Sept, 2024",
      status: "completed",
      statusColor: "green"
    },
    {
      id: 4,
      courseTitle: "Course Title Number 3",
      courseCode: "CSC 603",
      date: "8, Sept, 2024",
      status: "completed",
      statusColor: "green"
    },
    {
      id: 5,
      courseTitle: "Course Title Number 4",
      courseCode: "CSC 604",
      date: "8, Sept, 2024",
      status: "completed",
      statusColor: "green"
    },
    {
      id: 6,
      courseTitle: "Course Title Number 5",
      courseCode: "CSC 605",
      date: "18, Sept, 2024",
      status: "completed",
      statusColor: "green"
    }
  ]);

  const courses = ["CSC616", "CSC601", "CSC602", "CSC603", "CSC604", "CSC605"];
  const statuses = ["completed", "ongoing", "upcoming"];

  const getStatusIcon = (status) => {
    switch (status) {
      case "completed":
        return <CheckCircle size={16} className="text-green-600" />;
      case "ongoing":
        return <Clock size={16} className="text-orange-600" />;
      case "upcoming":
        return <AlertCircle size={16} className="text-red-600" />;
      default:
        return null;
    }
  };

  const getStatusClass = (status) => {
    switch (status) {
      case "completed":
        return "text-green-600";
      case "ongoing":
        return "text-orange-600";
      case "upcoming":
        return "text-red-600";
      default:
        return "text-gray-600";
    }
  };

  const filteredExams = exams.filter(exam => {
    const matchesSearch = exam.courseTitle.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         exam.courseCode.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCourse = selectedCourse === "" || exam.courseCode === selectedCourse;
    const matchesStatus = selectedStatus === "" || exam.status === selectedStatus;
    return matchesSearch && matchesCourse && matchesStatus;
  });

  const handleViewDetails = (examId) => {
    console.log("View details for exam:", examId);
    // Navigate to exam details or show modal
  };

  const handleEditExam = (examId) => {
    console.log("Edit exam:", examId);
    // Navigate to edit exam page
    navigate('/lecturer/create-exam', { state: { examId, mode: 'edit' } });
  };

  const handleDeleteExam = (examId) => {
    console.log("Delete exam:", examId);
    // Show confirmation and delete
    if (window.confirm("Are you sure you want to delete this exam?")) {
      // Delete logic here
      alert("Exam deleted successfully!");
    }
  };

  return (
    <div className="min-h-screen">
      {/* Header */}
      <div className="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4">
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
          <h1 className="text-xl sm:text-2xl font-semibold text-gray-800">Exam Management</h1>
          <button 
            onClick={() => navigate('/lecturer/create-exam')}
            className="flex items-center justify-center space-x-2 px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base"
          >
            <Plus size={18} className="sm:w-5 sm:h-5" />
            <span>Create New Exam</span>
          </button>
        </div>
      </div>

      {/* Filters Section */}
      <div className="bg-white mx-4 sm:mx-6 mt-4 sm:mt-6 rounded-lg border border-gray-200 p-4 sm:p-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          {/* Keywords Search */}
          <div>
            <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Keywords</label>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={16} />
              <input
                type="text"
                placeholder="Search"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          {/* Course Filter */}
          <div>
            <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Course</label>
            <select
              value={selectedCourse}
              onChange={(e) => setSelectedCourse(e.target.value)}
              className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">CSC616</option>
              {courses.map(course => (
                <option key={course} value={course}>{course}</option>
              ))}
            </select>
          </div>

          {/* Status Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              value={selectedStatus}
              onChange={(e) => setSelectedStatus(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Completed</option>
              {statuses.map(status => (
                <option key={status} value={status}>
                  {status.charAt(0).toUpperCase() + status.slice(1)}
                </option>
              ))}
            </select>
          </div>

          {/* Date Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Date</label>
            <div className="relative">
              <input
                type="text"
                value={selectedDate}
                onChange={(e) => setSelectedDate(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <Calendar className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
            </div>
          </div>
        </div>

        {/* Filter Button */}
        <div className="flex justify-end mt-4">
          <button className="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <Filter size={20} />
            <span>Filter</span>
          </button>
        </div>
      </div>

      {/* Exams Table */}
      <div className="mx-6 mt-6 bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-600 text-white">
              <th className="px-6 py-4 text-left text-sm font-medium">
                <input type="checkbox" className="rounded border-gray-300" />
              </th>
              <th className="px-6 py-4 text-left text-sm font-medium">Course Title</th>
              <th className="px-6 py-4 text-left text-sm font-medium">Course Code</th>
              <th className="px-6 py-4 text-left text-sm font-medium">Date</th>
              <th className="px-6 py-4 text-left text-sm font-medium">Status</th>
              <th className="px-6 py-4 text-left text-sm font-medium">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {filteredExams.map((exam, index) => (
              <tr key={exam.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap">
                  <input type="checkbox" className="rounded border-gray-300" />
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">{exam.courseTitle}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm text-gray-900">{exam.courseCode}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm text-gray-900">{exam.date}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center space-x-2">
                    {getStatusIcon(exam.status)}
                    <span className={`text-sm font-medium capitalize ${getStatusClass(exam.status)}`}>
                      {exam.status}
                    </span>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center space-x-3">
                    <button 
                      onClick={() => handleViewDetails(exam.id)}
                      className="text-blue-600 hover:text-blue-700 text-sm bg-blue-50 px-3 py-1 rounded border border-blue-200"
                    >
                      View Details
                    </button>
                    <button 
                      onClick={() => handleEditExam(exam.id)}
                      className="text-gray-600 hover:text-gray-700"
                    >
                      <Edit size={16} />
                    </button>
                    <button 
                      onClick={() => handleDeleteExam(exam.id)}
                      className="text-red-600 hover:text-red-700"
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filteredExams.length === 0 && (
          <div className="text-center py-8">
            <div className="text-gray-500">No exams found matching your criteria.</div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ExamManagement;
