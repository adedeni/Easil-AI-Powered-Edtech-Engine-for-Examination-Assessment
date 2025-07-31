import React, { useState } from "react";
import { 
  Search, 
  Filter, 
  Download
} from "lucide-react";

const Results = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedDepartment, setSelectedDepartment] = useState("Computer Science");
  const [selectedCourse, setSelectedCourse] = useState("CSC616");
  const [selectedDate, setSelectedDate] = useState("10/11/2024");
  
  // Active filter tags
  const [activeFilters, setActiveFilters] = useState([
    { id: 1, label: "Computer Science", color: "bg-blue-100 text-blue-800" },
    { id: 2, label: "CSC616", color: "bg-green-100 text-green-800" },
    { id: 3, label: "10/11/2024", color: "bg-purple-100 text-purple-800" }
  ]);

  // Sample students data with results
  const [students] = useState([
    {
      id: 1,
      name: "Blessing Sharon",
      matricNo: "CSC/2023/123",
      course: "CSC616",
      status: "Passed",
      score: "70 (A)",
      avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 2,
      name: "Student Name 2",
      matricNo: "CSC/2023/123",
      course: "CSC616",
      status: "Passed",
      score: "60 (B)",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 3,
      name: "Student Name 3",
      matricNo: "CSC/2023/123",
      course: "CSC616",
      status: "Failed",
      score: "12F",
      avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 4,
      name: "Student Name 4",
      matricNo: "CSC/2023/123",
      course: "CSC616",
      status: "Passed",
      score: "60 (B)",
      avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 5,
      name: "Student Name 5",
      matricNo: "CSC/2023/123",
      course: "CSC616",
      status: "Passed",
      score: "60 (B)",
      avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face"
    }
  ]);

  const removeFilter = (filterId) => {
    setActiveFilters(prev => prev.filter(filter => filter.id !== filterId));
  };

  const clearAllFilters = () => {
    setActiveFilters([]);
  };

  const getStatusColor = (status) => {
    return status === "Passed" 
      ? "bg-green-100 text-green-800" 
      : "bg-red-100 text-red-800";
  };

  // Statistics data
  const stats = {
    totalStudents: 300,
    averageScore: 80,
    totalAbsentStudents: 30,
    totalFinishedStudents: 270,
    totalPassedStudents: 250,
    totalFailedStudents: 20
  };

  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex justify-between items-center mb-6 sm:mb-8">
          <h1 className="text-xl sm:text-2xl font-bold text-gray-800">Results Management</h1>
        </div>

        {/* Filters */}
        <div className="bg-white rounded-lg p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-3 sm:mb-4">
            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Keywords</label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <input
                  type="text"
                  placeholder="Search"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Department</label>
              <select
                value={selectedDepartment}
                onChange={(e) => setSelectedDepartment(e.target.value)}
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="Computer Science">Computer Science</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Physics">Physics</option>
              </select>
            </div>

            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Course</label>
              <input
                type="text"
                value={selectedCourse}
                onChange={(e) => setSelectedCourse(e.target.value)}
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="CSC616"
              />
            </div>

            <div className="flex items-end space-x-2">
              <div className="flex-1">
                <label className="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <select
                  value={selectedDate}
                  onChange={(e) => setSelectedDate(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="10/11/2024">10/11/2024</option>
                  <option value="11/11/2024">11/11/2024</option>
                  <option value="12/11/2024">12/11/2024</option>
                </select>
              </div>
              <button className="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <Filter className="w-4 h-4" />
                <span>Filter</span>
              </button>
            </div>
          </div>

          {/* Active Filters */}
          <div className="flex items-center space-x-2 flex-wrap">
            {activeFilters.map((filter) => (
              <span key={filter.id} className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${filter.color}`}>
                {filter.label}
                <button
                  onClick={() => removeFilter(filter.id)}
                  className="ml-2 text-xs hover:text-gray-700"
                >
                  ×
                </button>
              </span>
            ))}
            {activeFilters.length > 0 && (
              <button
                onClick={clearAllFilters}
                className="text-sm text-gray-600 hover:text-gray-800 underline"
              >
                Clear All
              </button>
            )}
          </div>
        </div>

        {/* Overview Section */}
        <div className="mb-8">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-800">Overview</h2>
            <button className="flex items-center space-x-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
              <Download className="w-4 h-4" />
              <span>Export</span>
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {/* Row 1 */}
            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-blue-600 text-lg">👥</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Total Students</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.totalStudents}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                  <span className="text-yellow-600 text-lg">⭐</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Average Score</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.averageScore}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                  <span className="text-orange-600 text-lg">⚠️</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Total Absent Students</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.totalAbsentStudents}</p>
                </div>
              </div>
            </div>

            {/* Row 2 */}
            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-blue-600 text-lg">✅</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Total Finished Students</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.totalFinishedStudents}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                  <span className="text-green-600 text-lg">✅</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Total Passed Students</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.totalPassedStudents}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
              <div className="flex items-center space-x-4">
                <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                  <span className="text-red-600 text-lg">❌</span>
                </div>
                <div>
                  <p className="text-sm text-gray-500 font-medium">Total Failed Students</p>
                  <p className="text-3xl font-bold text-gray-900">{stats.totalFailedStudents}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Enrolled Students Section */}
        <div className="bg-white rounded-lg shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-800">Enrolled Students</h3>
          </div>
          
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-600 text-white">
                  <th className="px-6 py-4 text-left text-sm font-medium">Student Name</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Matric No.</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Course</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Passed/Failed</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Score</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Details</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {students.map((student) => (
                  <tr key={student.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 rounded-full overflow-hidden">
                          <img 
                            src={student.avatar} 
                            alt={student.name}
                            className="w-full h-full object-cover"
                          />
                        </div>
                        <span className="text-sm font-medium text-gray-900">{student.name}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">{student.matricNo}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{student.course}</td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(student.status)}`}>
                        {student.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">{student.score}</td>
                    <td className="px-6 py-4">
                      <button className="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                        View Details
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Results;
