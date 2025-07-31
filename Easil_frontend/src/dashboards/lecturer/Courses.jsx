import React, { useState } from "react";
import { Search, Filter, Plus, Eye, Edit, Trash2, Upload } from "lucide-react";

const Courses = () => {
  const [showAddModal, setShowAddModal] = useState(false);
  const [showDetailsModal, setShowDetailsModal] = useState(false);
  const [selectedCourse, setSelectedCourse] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [filterDepartment, setFilterDepartment] = useState("Computer Science");
  const [filterCourse, setFilterCourse] = useState("CSC616");
  const [filterStatus, setFilterStatus] = useState("Active");

  // Sample course data
  const [courses, setCourses] = useState([
    {
      id: 1,
      title: "Fundamentals of E-Commerce",
      code: "CSC 616",
      status: "Active",
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)",
      description: "EC involves conducting transactions electronically. Its major categories are pure versus partial EC, Internet versus non-Internet, and electronic markets versus company-based systems...",
      materials: [
        { name: "Introduction to E-Commerce Module 1 Lecture 2 rev", size: "3.5MB" }
      ]
    },
    {
      id: 2,
      title: "Course Title Number 2",
      code: "CSC 601",
      status: "Active",
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)"
    },
    {
      id: 3,
      title: "Course Title Number 3", 
      code: "CSC 602",
      status: "Active",
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)"
    },
    {
      id: 4,
      title: "Course Title Number 4",
      code: "CSC 603", 
      status: "Active",
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)"
    },
    {
      id: 5,
      title: "Course Title Number 5",
      code: "CSC 604",
      status: "Inactive",
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)"
    },
    {
      id: 6,
      title: "Course Title Number 6",
      code: "CSC 605",
      status: "Inactive", 
      units: 3,
      coordinator: "Ikono, Rhoda (PhD)"
    }
  ]);

  const [newCourse, setNewCourse] = useState({
    title: "",
    code: "",
    description: "",
    status: "Active",
    coordinator: "Ikono, Rhoda (PhD)",
    units: 3
  });

  const handleAddCourse = () => {
    if (newCourse.title && newCourse.code) {
      setCourses([...courses, { 
        ...newCourse, 
        id: courses.length + 1,
        materials: []
      }]);
      setNewCourse({
        title: "",
        code: "", 
        description: "",
        status: "Active",
        coordinator: "Ikono, Rhoda (PhD)",
        units: 3
      });
      setShowAddModal(false);
    }
  };

  const handleViewDetails = (course) => {
    setSelectedCourse(course);
    setShowDetailsModal(true);
  };

  const filteredCourses = courses.filter(course => 
    course.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
    course.code.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div>
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h1 className="text-xl sm:text-2xl font-semibold text-gray-800">Course Management</h1>
        <button 
          onClick={() => setShowAddModal(true)}
          className="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors text-sm sm:text-base"
        >
          <Plus size={18} className="sm:w-5 sm:h-5" />
          <span>Add New Course</span>
        </button>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 shadow-sm">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-3 sm:mb-4">
          <div>
            <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Keywords</label>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={16} />
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
            <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Department</label>
            <select 
              value={filterDepartment}
              onChange={(e) => setFilterDepartment(e.target.value)}
              className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option>Computer Science</option>
              <option>Mathematics</option>
              <option>Physics</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <input
              type="text"
              placeholder="CSC616"
              value={filterCourse}
              onChange={(e) => setFilterCourse(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              value={filterStatus}
              onChange={(e) => setFilterStatus(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option>Active</option>
              <option>Inactive</option>
            </select>
          </div>
        </div>

        <button className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
          <Filter size={20} />
          <span>Filter</span>
        </button>
      </div>

      {/* Courses Table */}
      <div className="bg-white rounded-lg shadow-sm overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-600 text-white">
            <tr>
              <th className="px-4 py-3 text-left">
                <input type="checkbox" className="rounded" />
              </th>
              <th className="px-4 py-3 text-left">Course Title</th>
              <th className="px-4 py-3 text-left">Course Code</th>
              <th className="px-4 py-3 text-left">Status</th>
              <th className="px-4 py-3 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            {filteredCourses.map((course) => (
              <tr key={course.id} className="border-b border-gray-200 hover:bg-gray-50">
                <td className="px-4 py-3">
                  <input type="checkbox" className="rounded" />
                </td>
                <td className="px-4 py-3">{course.title}</td>
                <td className="px-4 py-3">{course.code}</td>
                <td className="px-4 py-3">
                  <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                    course.status === 'Active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    <div className={`w-2 h-2 rounded-full mr-2 ${
                      course.status === 'Active' ? 'bg-green-500' : 'bg-red-500'
                    }`}></div>
                    {course.status}
                  </span>
                </td>
                <td className="px-4 py-3">
                  <div className="flex items-center space-x-2">
                    <button 
                      onClick={() => handleViewDetails(course)}
                      className="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50"
                    >
                      View Details
                    </button>
                    <button className="text-gray-600 hover:text-blue-600">
                      <Edit size={16} />
                    </button>
                    <button className="text-gray-600 hover:text-red-600">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Add Course Modal */}
      {showAddModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 className="text-xl font-semibold mb-4">Add Course</h2>
            
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                  <input
                    type="text"
                    value={newCourse.title}
                    onChange={(e) => setNewCourse({...newCourse, title: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Course Code</label>
                  <input
                    type="text"
                    value={newCourse.code}
                    onChange={(e) => setNewCourse({...newCourse, code: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  rows={4}
                  value={newCourse.description}
                  onChange={(e) => setNewCourse({...newCourse, description: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                  <select 
                    value={newCourse.status}
                    onChange={(e) => setNewCourse({...newCourse, status: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option>Active</option>
                    <option>Inactive</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Course Coordinator</label>
                  <select 
                    value={newCourse.coordinator}
                    onChange={(e) => setNewCourse({...newCourse, coordinator: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option>Ikono, Rhoda (PhD)</option>
                    <option>Other Coordinator</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Upload Course Materials</label>
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                  <Upload className="mx-auto mb-2 text-gray-400" size={24} />
                  <p className="text-sm text-gray-600">
                    Drag and drop files here or <button className="text-blue-600 underline">Choose files</button>
                  </p>
                </div>
              </div>
            </div>

            <div className="flex justify-end space-x-3 mt-6">
              <button 
                onClick={() => setShowAddModal(false)}
                className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button 
                onClick={handleAddCourse}
                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Course Details Modal */}
      {showDetailsModal && selectedCourse && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-semibold">Course Details</h2>
              <button 
                onClick={() => setShowDetailsModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                ✕
              </button>
            </div>
            
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="flex items-center">
                  <span className="text-gray-600 mr-2">📚</span>
                  <div>
                    <span className="font-medium">Course Title:</span> {selectedCourse.title}
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div>
                    <span className="font-medium">Status</span>
                  </div>
                  <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                    selectedCourse.status === 'Active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    <div className={`w-2 h-2 rounded-full mr-2 ${
                      selectedCourse.status === 'Active' ? 'bg-green-500' : 'bg-red-500'
                    }`}></div>
                    {selectedCourse.status}
                  </span>
                </div>
              </div>

              <div className="flex items-center">
                <span className="text-gray-600 mr-2">📚</span>
                <div>
                  <span className="font-medium">Course Code:</span> {selectedCourse.code}
                </div>
              </div>

              <div className="flex items-center">
                <span className="text-gray-600 mr-2">📚</span>
                <div>
                  <span className="font-medium">Units:</span> {selectedCourse.units}
                </div>
              </div>

              <div className="flex items-center">
                <span className="text-gray-600 mr-2">📚</span>
                <div>
                  <span className="font-medium">Course Coordinator:</span> {selectedCourse.coordinator}
                </div>
              </div>

              {selectedCourse.description && (
                <div>
                  <h3 className="font-semibold mb-2">Course Description</h3>
                  <p className="text-gray-700 text-sm leading-relaxed">{selectedCourse.description}</p>
                </div>
              )}

              {selectedCourse.materials && selectedCourse.materials.length > 0 && (
                <div>
                  <h3 className="font-semibold mb-2">Course Material</h3>
                  {selectedCourse.materials.map((material, index) => (
                    <div key={index} className="flex items-center space-x-2 text-sm">
                      <span className="text-gray-600">📄</span>
                      <span>{material.name}</span>
                      <span className="text-gray-500">{material.size}</span>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Courses;
