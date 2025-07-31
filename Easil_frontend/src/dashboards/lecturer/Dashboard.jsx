import React from "react";
import { Users, BookOpen, FileText, ClipboardList, Users2, FolderOpen } from "lucide-react";
import { Link } from "react-router-dom";

const Dashboard = () => {
  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto">
        {/* Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <div className="bg-white rounded-lg border border-gray-300 p-4 sm:p-6">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <Users className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
              </div>
              <div className="min-w-0">
                <div className="text-xs sm:text-sm text-gray-600">Total Students</div>
                <div className="text-2xl sm:text-3xl font-bold text-gray-800">300</div>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg border border-gray-300 p-4 sm:p-6">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <BookOpen className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
              </div>
              <div className="min-w-0">
                <div className="text-xs sm:text-sm text-gray-600">Total Courses</div>
                <div className="text-2xl sm:text-3xl font-bold text-gray-800">1</div>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg border border-gray-300 p-4 sm:p-6">
            <div className="flex items-center space-x-3 sm:space-x-4">
              <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                <FileText className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
              </div>
              <div className="min-w-0">
                <div className="text-xs sm:text-sm text-gray-600">Total Assessments</div>
                <div className="text-2xl sm:text-3xl font-bold text-gray-800">10</div>
              </div>
            </div>
          </div>
        </div>

        {/* Action Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <Link to="/lecturer/exam-management" className="group">
            <div className="bg-blue-500 text-white rounded-lg p-4 sm:p-6 lg:p-8 hover:bg-blue-600 transition-colors cursor-pointer">
              <div className="flex items-start space-x-3 sm:space-x-4 mb-3 sm:mb-4">
                <ClipboardList className="h-6 w-6 sm:h-8 sm:w-8 flex-shrink-0" />
                <div className="min-w-0">
                  <h3 className="text-lg sm:text-xl font-semibold mb-1 sm:mb-2">Manage Assessments</h3>
                  <p className="text-blue-100 text-xs sm:text-sm leading-relaxed">Create, assign and track assessments with ease</p>
                </div>
              </div>
              <div className="flex justify-end">
                <svg className="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </Link>

          <Link to="/lecturer/students" className="group">
            <div className="bg-green-500 text-white rounded-lg p-4 sm:p-6 lg:p-8 hover:bg-green-600 transition-colors cursor-pointer">
              <div className="flex items-start space-x-3 sm:space-x-4 mb-3 sm:mb-4">
                <Users2 className="h-6 w-6 sm:h-8 sm:w-8 flex-shrink-0" />
                <div className="min-w-0">
                  <h3 className="text-lg sm:text-xl font-semibold mb-1 sm:mb-2">Manage Students</h3>
                  <p className="text-green-100 text-xs sm:text-sm leading-relaxed">Access and analyze student assessment results.</p>
                </div>
              </div>
              <div className="flex justify-end">
                <svg className="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </Link>

          <Link to="/lecturer/courses" className="group">
            <div className="bg-orange-500 text-white rounded-lg p-4 sm:p-6 lg:p-8 hover:bg-orange-600 transition-colors cursor-pointer">
              <div className="flex items-start space-x-3 sm:space-x-4 mb-3 sm:mb-4">
                <FolderOpen className="h-6 w-6 sm:h-8 sm:w-8 flex-shrink-0" />
                <div className="min-w-0">
                  <h3 className="text-lg sm:text-xl font-semibold mb-1 sm:mb-2">Manage Courses</h3>
                  <p className="text-orange-100 text-xs sm:text-sm leading-relaxed">Access and analyze courses and results</p>
                </div>
              </div>
              <div className="flex justify-end">
                <svg className="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
