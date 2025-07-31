import { Clock, BookOpen } from "lucide-react"
import { Link } from "react-router-dom"

const Assessments = () => {
  // Placeholder data for assessments
  const assessments = [
    {
      id: 1,
      title: "Fundamentals of E-Commerce - CSC 616",
      description:
        "This test includes 10 theory-based questions covering key e-commerce concepts like business models, payment systems, and digital marketing. You'll receive instant feedback after submission, with final verified scores after lecturer review.",
      duration: "20 min",
      startsIn: "4 hours",
      status: "upcoming",
    },
    {
      id: 2,
      title: "Advanced Computer Graphics - CSC 610",
      description:
        "This test consists of 10 questions focused on advanced topics in computer graphics, including rendering, modeling, and animation. Instant feedback is provided upon submission, with verified scores after lecturer review.",
      duration: "25 min",
      startsIn: "6 hours",
      status: "upcoming",
    },
    {
      id: 3,
      title: "Parallel Computing Systems - CSC 612",
      description:
        "The test contains 10 questions on parallel computing concepts, covering topics such as parallel algorithms, architectures, and performance optimization. Immediate feedback will be given after submission, followed by final verified scores after review.",
      duration: "30 min",
      startsIn: "5 hours",
      status: "upcoming",
    },
    {
      id: 4,
      title: "Theory of Programming Languages - CSC 617",
      description:
        "This test features 10 questions on the fundamental theories behind programming languages, including syntax, semantics, and language design. Instant feedback is available post-submission, with verified scores provided after lecturer approval.",
      duration: "25 min",
      startsIn: "8 hours",
      status: "upcoming",
    },
  ]

  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 className="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Available Assessments</h2>

        {/* Assessments Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
          {assessments.map((assessment) => (
            <div
              key={assessment.id}
              className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow"
            >
              {/* Course Icon */}
              <div className="flex items-start space-x-3 mb-3 sm:mb-4">
                <div className="w-7 h-7 sm:w-8 sm:h-8 bg-blue-100 rounded flex items-center justify-center flex-shrink-0">
                  <BookOpen className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600" />
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="font-semibold text-gray-800 text-base sm:text-lg mb-2 line-clamp-2">{assessment.title}</h3>
                </div>
              </div>

              {/* Description */}
              <p className="text-gray-600 text-sm mb-3 sm:mb-4 leading-relaxed line-clamp-3">{assessment.description}</p>

              {/* Duration and Time Info */}
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                <div className="flex items-center space-x-2 text-gray-500">
                  <Clock className="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" />
                  <span className="text-xs sm:text-sm">{assessment.duration}</span>
                </div>
                <div className="flex items-center space-x-2 text-gray-500">
                  <Clock className="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" />
                  <span className="text-xs sm:text-sm">Starts in {assessment.startsIn}</span>
                </div>
              </div>

              {/* Action Button */}
              <div className="flex justify-end">
                <Link 
                  to={`/student/assessments/${assessment.id}`}
                  className="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors"
                >
                  View Details →
                </Link>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default Assessments
