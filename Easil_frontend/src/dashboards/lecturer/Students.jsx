import React, { useState } from "react";
import { 
  Search, 
  Filter, 
  Eye, 
  Edit, 
  Trash2,
  UserPlus,
  X,
  Camera,
  Check
} from "lucide-react";

const Students = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [showAddModal, setShowAddModal] = useState(false);
  const [showViewModal, setShowViewModal] = useState(false);
  const [selectedStudent, setSelectedStudent] = useState(null);

  // Sample student data matching the design
  const [students] = useState([
    {
      id: 1,
      name: "Adeola Adebayo",
      email: "adeola.adebayo@gmail.com",
      matricNumber: "CSC/2024/35",
      avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 2,
      name: "Chidi Okeke",
      email: "chidi.okeke@yahoo.com",
      matricNumber: "CSC/2024/37",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 3,
      name: "Amina Suleiman",
      email: "amina.suleiman@hotmail.com",
      matricNumber: "CSC/2024/45",
      avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 4,
      name: "Emeka Nwosu",
      email: "emeka.nwosu@gmail.com",
      matricNumber: "CSC/2024/155",
      avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 5,
      name: "Ifeonyi Eze",
      email: "ifeonyi.eze@outlook.com",
      matricNumber: "CSC/2024/65",
      avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 6,
      name: "Yewande Alabi",
      email: "yewande.alabi@gmail.com",
      matricNumber: "CSC/2024/75",
      avatar: "https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 7,
      name: "Tosin Olufemi",
      email: "tosin.olufemi@yahoo.com",
      matricNumber: "CSC/2024/18",
      avatar: "https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 8,
      name: "Zainab Hassan",
      email: "zainab.hassan@gmail.com",
      matricNumber: "CSC/2024/41",
      avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face"
    },
    {
      id: 9,
      name: "Kolawole Ogunleye",
      email: "kolawole.ogunleye@gmail.com",
      matricNumber: "CSC/2024/60",
      avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=40&h=40&fit=crop&crop=face"
    }
  ]);

  const [newStudent, setNewStudent] = useState({
    name: "",
    email: "",
    matricNumber: "",
    profilePicture: null,
    enrolledCourses: []
  });

  // Available courses for enrollment
  const availableCourses = [
    { title: "Fundamentals of E-Commerce", code: "CSC 616", units: 3 },
    { title: "Data Structures and Algorithms", code: "CSC 601", units: 3 },
    { title: "Software Engineering", code: "CSC 602", units: 3 },
    { title: "Database Systems", code: "CSC 603", units: 3 },
    { title: "Computer Networks", code: "CSC 604", units: 3 }
  ];

  const filteredStudents = students.filter(student =>
    student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    student.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    student.matricNumber.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleViewStudent = (student) => {
    setSelectedStudent(student);
    setShowViewModal(true);
  };

  const handleAddStudent = () => {
    // Here you would typically make an API call to add the student
    console.log("Adding student:", newStudent);
    
    // Reset form and close modal
    setNewStudent({
      name: "",
      email: "",
      matricNumber: "",
      profilePicture: null,
      enrolledCourses: []
    });
    setShowAddModal(false);
  };

  const handleCourseToggle = (courseCode) => {
    setNewStudent(prev => ({
      ...prev,
      enrolledCourses: prev.enrolledCourses.includes(courseCode)
        ? prev.enrolledCourses.filter(code => code !== courseCode)
        : [...prev.enrolledCourses, courseCode]
    }));
  };

  const AddStudentModal = () => {
    if (!showAddModal) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          {/* Header */}
          <div className="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 className="text-xl font-bold text-gray-800">Add New Student</h2>
            <button 
              onClick={() => setShowAddModal(false)}
              className="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          <div className="p-6">
            {/* Profile Picture Section */}
            <div className="flex items-center space-x-4 mb-6">
              <div className="w-20 h-20 bg-amber-600 rounded-full flex items-center justify-center overflow-hidden">
                {newStudent.profilePicture ? (
                  <img 
                    src={newStudent.profilePicture} 
                    alt="Profile" 
                    className="w-full h-full object-cover"
                  />
                ) : (
                  <span className="text-white text-2xl font-bold">
                    {newStudent.name.split(' ').map(n => n[0]).join('') || '?'}
                  </span>
                )}
              </div>
              <button className="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                <Camera className="w-4 h-4" />
                <span>Add Profile Picture</span>
              </button>
            </div>

            {/* Form Fields */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Student Name
                </label>
                <input
                  type="text"
                  value={newStudent.name}
                  onChange={(e) => setNewStudent(prev => ({ ...prev, name: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter student name"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Matric/Registration Number
                </label>
                <input
                  type="text"
                  value={newStudent.matricNumber}
                  onChange={(e) => setNewStudent(prev => ({ ...prev, matricNumber: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., CSC/2024/001"
                />
              </div>
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Student Email
              </label>
              <input
                type="email"
                value={newStudent.email}
                onChange={(e) => setNewStudent(prev => ({ ...prev, email: e.target.value }))}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter student email"
              />
            </div>

            {/* Enrolled Courses Section */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-800 mb-4">Enrolled Courses</h3>
              <div className="bg-gray-50 rounded-lg p-4">
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="bg-gray-600 text-white">
                        <th className="px-4 py-3 text-left text-sm font-medium">Course Title</th>
                        <th className="px-4 py-3 text-left text-sm font-medium">Course Code</th>
                        <th className="px-4 py-3 text-left text-sm font-medium">Units</th>
                        <th className="px-4 py-3 text-left text-sm font-medium">Selected</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {availableCourses.map((course) => (
                        <tr key={course.code} className="bg-white hover:bg-gray-50">
                          <td className="px-4 py-3 text-sm text-gray-900">{course.title}</td>
                          <td className="px-4 py-3 text-sm text-gray-900">{course.code}</td>
                          <td className="px-4 py-3 text-sm text-gray-900">{course.units}</td>
                          <td className="px-4 py-3">
                            <input
                              type="checkbox"
                              checked={newStudent.enrolledCourses.includes(course.code)}
                              onChange={() => handleCourseToggle(course.code)}
                              className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          {/* Footer */}
          <div className="flex justify-end space-x-4 p-6 border-t border-gray-200 bg-gray-50">
            <button 
              onClick={() => setShowAddModal(false)}
              className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200"
            >
              Cancel
            </button>
            <button 
              onClick={handleAddStudent}
              className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:shadow-lg transition-all duration-200"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    );
  };

  const ViewStudentModal = () => {
    if (!showViewModal || !selectedStudent) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          {/* Header */}
          <div className="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 className="text-xl font-bold text-gray-800">Student Details</h2>
            <button 
              onClick={() => setShowViewModal(false)}
              className="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          <div className="p-6">
            {/* Student Profile */}
            <div className="flex items-center space-x-6 mb-8">
              <div className="relative">
                <img
                  src={selectedStudent.avatar}
                  alt={selectedStudent.name}
                  className="w-20 h-20 rounded-full object-cover"
                  onError={(e) => {
                    e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedStudent.name)}&background=random`;
                  }}
                />
                <div className="absolute -top-1 -right-1">
                  <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Active
                  </span>
                </div>
              </div>
              
              <div className="flex-1">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                <div className="grid grid-cols-1 gap-3">
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Name:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.name}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Matric Number:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.matricNumber}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Email:</span>
                    <span className="text-gray-900 font-medium">{selectedStudent.email}</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-4 h-4 border-2 border-gray-400 rounded-sm"></div>
                    <span className="text-sm font-medium text-gray-600 w-32">Department:</span>
                    <span className="text-gray-900 font-medium">Computer Science</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Academic Information */}
            <div className="mb-8">
              <div className="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold mb-6 shadow-md">
                Academic Information
              </div>

              <div className="bg-gray-50 rounded-lg p-6">
                <div className="grid grid-cols-2 gap-6">
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Enrollment Date</span>
                    <span className="text-gray-900 font-bold">January 15, 2024</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Student Status</span>
                    <span className="text-green-600 font-bold">Active</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">Current Level</span>
                    <span className="text-blue-600 font-bold">400 Level</span>
                  </div>
                  <div className="flex justify-between items-center py-2 border-b border-gray-200">
                    <span className="text-gray-600 font-medium">CGPA</span>
                    <span className="text-green-600 font-bold">4.25</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Enrolled Courses */}
            <div className="mb-8">
              <div className="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold mb-6 shadow-md">
                Enrolled Courses
              </div>

              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="bg-gray-600 text-white">
                      <th className="px-4 py-3 text-left text-sm font-medium">Course Title</th>
                      <th className="px-4 py-3 text-left text-sm font-medium">Course Code</th>
                      <th className="px-4 py-3 text-left text-sm font-medium">Units</th>
                      <th className="px-4 py-3 text-left text-sm font-medium">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {availableCourses.slice(0, 3).map((course) => (
                      <tr key={course.code} className="bg-white hover:bg-gray-50">
                        <td className="px-4 py-3 text-sm text-gray-900">{course.title}</td>
                        <td className="px-4 py-3 text-sm text-gray-900">{course.code}</td>
                        <td className="px-4 py-3 text-sm text-gray-900">{course.units}</td>
                        <td className="px-4 py-3">
                          <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enrolled
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            {/* Recent Assessment Performance */}
            <div className="mb-6">
              <div className="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold mb-6 shadow-md">
                Recent Assessment Performance
              </div>

              <div className="space-y-4">
                <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                  <div className="flex justify-between items-center mb-2">
                    <h4 className="font-semibold text-gray-800">Mid-Semester Exam - CSC 616</h4>
                    <span className="text-green-600 font-bold">85%</span>
                  </div>
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Date: September 6, 2024</span>
                    <span>Grade: A</span>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                  <div className="flex justify-between items-center mb-2">
                    <h4 className="font-semibold text-gray-800">Quiz 2 - CSC 601</h4>
                    <span className="text-blue-600 font-bold">78%</span>
                  </div>
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Date: August 28, 2024</span>
                    <span>Grade: B+</span>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                  <div className="flex justify-between items-center mb-2">
                    <h4 className="font-semibold text-gray-800">Assignment 1 - CSC 602</h4>
                    <span className="text-green-600 font-bold">92%</span>
                  </div>
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Date: August 20, 2024</span>
                    <span>Grade: A</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Footer */}
          <div className="flex justify-end space-x-4 p-6 border-t border-gray-200 bg-gray-50">
            <button 
              onClick={() => setShowViewModal(false)}
              className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200"
            >
              Close
            </button>
            <button className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:shadow-lg transition-all duration-200">
              Edit Student
            </button>
          </div>
        </div>
      </div>
    );
  };

  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 sm:mb-8 space-y-3 sm:space-y-0">
          <h1 className="text-xl sm:text-2xl font-bold text-gray-800">Student Management</h1>
          <button 
            onClick={() => setShowAddModal(true)}
            className="flex items-center justify-center space-x-2 px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base"
          >
            <UserPlus className="w-4 h-4" />
            <span>Add New Student</span>
          </button>
        </div>

        {/* Search and Filter */}
        <div className="bg-white rounded-lg p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
          <div className="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <div className="flex-1">
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                Keywords
              </label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <input
                  type="text"
                  placeholder="Search"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>
            <div className="pt-6">
              <button className="flex items-center space-x-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <Filter className="w-4 h-4" />
                <span>Filter</span>
              </button>
            </div>
          </div>
        </div>

        {/* Students Table */}
        <div className="bg-white rounded-lg shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-600 text-white">
                  <th className="px-6 py-4 text-left text-sm font-medium">Student Name</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Email</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Matric Number</th>
                  <th className="px-6 py-4 text-left text-sm font-medium">Details</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {filteredStudents.map((student) => (
                  <tr key={student.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-3">
                        <img
                          src={student.avatar}
                          alt={student.name}
                          className="w-8 h-8 rounded-full object-cover"
                          onError={(e) => {
                            e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name)}&background=random`;
                          }}
                        />
                        <span className="text-sm font-medium text-gray-900">{student.name}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">{student.email}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">{student.matricNumber}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center space-x-2">
                        <button 
                          onClick={() => handleViewStudent(student)}
                          className="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                        >
                          View Details
                        </button>
                        <button className="p-1 text-gray-400 hover:text-gray-600">
                          <Edit className="w-4 h-4" />
                        </button>
                        <button className="p-1 text-gray-400 hover:text-red-600">
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Add Student Modal */}
      <AddStudentModal />

      {/* View Student Modal */}
      <ViewStudentModal />
    </div>
  );
};

export default Students;
