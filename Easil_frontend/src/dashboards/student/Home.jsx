import { User, FileText, CheckCircle, Settings, ChevronRight } from "lucide-react"
import { useNavigate } from "react-router-dom"

const Home = () => {
  const navigate = useNavigate();

  // Dashboard stats matching the design
  const stats = [
    {
      title: "Upcoming Assessment",
      value: "3",
      icon: User,
      bgColor: "bg-gray-100",
      iconColor: "text-gray-600",
    },
    {
      title: "Total Courses",
      value: "8",
      icon: User,
      bgColor: "bg-gray-100",
      iconColor: "text-gray-600",
    },
    {
      title: "Total Assessments",
      value: "10",
      icon: User,
      bgColor: "bg-gray-100",
      iconColor: "text-gray-600",
    },
  ]

  const actionCards = [
    {
      title: "View Assessments",
      description: "Access your upcoming and completed assessments",
      icon: FileText,
      bgColor: "bg-blue-500",
      textColor: "text-white",
      path: "/student/assessments"
    },
    {
      title: "Check Results",
      description: "View your assessment scores and feedback",
      icon: CheckCircle,
      bgColor: "bg-green-500",
      textColor: "text-white",
      path: "/student/results"
    },
    {
      title: "My Courses",
      description: "Manage your enrolled courses and materials",
      icon: Settings,
      bgColor: "bg-orange-500",
      textColor: "text-white",
      path: "/student/courses"
    },
  ]

  const handleCardClick = (path) => {
    navigate(path);
  }

  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 className="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Dashboard</h2>
        
        {/* Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
          {stats.map((stat, index) => (
            <div key={index} className="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
              <div className="flex items-center space-x-3 sm:space-x-4">
                <div className={`w-10 h-10 sm:w-12 sm:h-12 ${stat.bgColor} rounded-full flex items-center justify-center`}>
                  <stat.icon className={`w-5 h-5 sm:w-6 sm:h-6 ${stat.iconColor}`} />
                </div>
                <div>
                  <p className="text-gray-600 text-xs sm:text-sm">{stat.title}</p>
                  <p className="text-xl sm:text-2xl font-bold text-gray-800">{stat.value}</p>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Action Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          {actionCards.map((card, index) => (
            <div
              key={index}
              onClick={() => handleCardClick(card.path)}
              className={`${card.bgColor} ${card.textColor} rounded-lg p-4 sm:p-6 cursor-pointer hover:opacity-90 transition-opacity`}
            >
              <div className="flex items-start justify-between mb-3 sm:mb-4">
                <card.icon className="w-6 h-6 sm:w-8 sm:h-8" />
                <ChevronRight className="w-5 h-5 sm:w-6 sm:h-6" />
              </div>
              <h3 className="text-lg sm:text-xl font-semibold mb-1 sm:mb-2">{card.title}</h3>
              <p className="text-xs sm:text-sm opacity-90 leading-relaxed">{card.description}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default Home
